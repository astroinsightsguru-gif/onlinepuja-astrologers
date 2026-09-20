<?php

namespace TaylanUnutmaz\AgoraTokenBuilder;

namespace App\AgoraToken;

class AccessToken
{
    const PRIVILEGES = array(
        "kJoinChannel" => 1,
        "kPublishAudioStream" => 2,
        "kPublishVideoStream" => 3,
        "kPublishDataStream" => 4,
        "kPublishAudioCdn" => 5,
        "kPublishVideoCdn" => 6,
        "kRequestPublishAudioStream" => 7,
        "kRequestPublishVideoStream" => 8,
        "kRequestPublishDataStream" => 9,
        "kInvitePublishAudioStream" => 10,
        "kInvitePublishVideoStream" => 11,
        "kInvitePublishDataStream" => 12,
        "kAdministrateChannel" => 101,
        "kRtmLogin" => 1000,
    );

    public $appID;
    public $appCertificate;
    public $channelName;
    public $uid;
    public $message;

    public function __construct()
    {
        $this->message = new Message();
    }

    public function setUid($uid)
    {
        if ($uid === 0) {
            $this->uid = "";
        } else {
            $this->uid = $uid . '';
        }
    }

    public function isnonemptystring($name, $str)
    {
        if (is_string($str) && $str !== "") {
            return true;
        }
        echo $name . " check failed, should be a non-empty string";
        return false;
    }

    public function init($appID, $appCertificate, $channelName, $uid)
    {
        $accessToken = new AccessToken();

        if (!$accessToken->isnonemptystring("appID", $appID) ||
            !$accessToken->isnonemptystring("appCertificate", $appCertificate) ||
            !$accessToken->isnonemptystring("channelName", $channelName)) {
            return null;
        }

        $accessToken->appID = $appID;
        $accessToken->appCertificate = $appCertificate;
        $accessToken->channelName = $channelName;

        $accessToken->setUid($uid);
        $accessToken->message = new Message();
        return $accessToken;
    }

    public function initWithToken($token, $appCertificate, $channel, $uid)
    {
        $accessToken = new AccessToken();
        if (!$accessToken->extract($token, $appCertificate, $channel, $uid)) {
            return null;
        }
        return $accessToken;
    }

    public function addPrivilege($key, $expireTimestamp)
    {
        $this->message->privileges[$key] = $expireTimestamp;
        return $this;
    }

    public function extract($token, $appCertificate, $channelName, $uid)
    {
        $verlen = 3;
        $appidlen = 32;
        $version = substr($token, 0, $verlen);
        if ($version !== "006") {
            echo 'invalid version ' . $version;
            return false;
        }

        if (!$this->isnonemptystring("token", $token) ||
            !$this->isnonemptystring("appCertificate", $appCertificate) ||
            !$this->isnonemptystring("channelName", $channelName)) {
            return false;
        }

        $appid = substr($token, $verlen, $appidlen);
        $content = (base64_decode(substr($token, $verlen + $appidlen, strlen($token) - ($verlen + $appidlen))));

        $pos = 0;
        $len = unpack("v", $content . substr($pos, 2))[1];
        $pos += 2;
        $pos += $len;
        $pos += 4;
        $pos += 4;
        $msgLen = unpack("v", substr($content, $pos, 2))[1];
        $pos += 2;
        $msg = substr($content, $pos, $msgLen);

        $this->appID = $appid;
        $message = new Message();
        $message->unpackContent($msg);
        $this->message = $message;

        //non reversable values
        $this->appCertificate = $appCertificate;
        $this->channelName = $channelName;
        $this->setUid($uid);
        return true;
    }

    public function build()
    {
        $msg = $this->message->packContent();
        $val = array_merge(unpack("C*", $this->appID), unpack("C*", $this->channelName), unpack("C*", $this->uid), $msg);

        $sig = hash_hmac('sha256', implode(array_map("chr", $val)), $this->appCertificate, true);

        $crcchannelname = crc32($this->channelName)&0xffffffff;
        $crcuid = crc32($this->uid)&0xffffffff;

        $content = array_merge(unpack("C*", $this->packString($sig)), unpack("C*", pack("V", $crcchannelname)), unpack("C*", pack("V", $crcuid)), unpack("C*", pack("v", count($msg))), $msg);
        $version = "006";
        return $version . $this->appID . base64_encode(implode(array_map("chr", $content)));
    }

    public function packString($value)
    {
        return pack("v", strlen($value)) . $value;
    }
}
