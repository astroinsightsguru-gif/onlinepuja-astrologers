<?php

namespace TaylanUnutmaz\AgoraTokenBuilder;
namespace App\AgoraToken;
class RtcTokenBuilder
{
    const ROLEATTENDEE = 0;
    const ROLEPUBLISHER = 1;
    const ROLESUBSCRIBER = 2;
    const ROLEADMIN = 101;
    public function buildTokenWithUid($appID, $appCertificate, $channelName, $uid, $role, $privilegeExpireTs){
        return RtcTokenBuilder::buildTokenWithUserAccount($appID, $appCertificate, $channelName, $uid, $role, $privilegeExpireTs);
    }
    public function buildTokenWithUserAccount($appID, $appCertificate, $channelName, $userAccount, $role, $privilegeExpireTs){
      $tokens = new AccessToken();
        $token = $tokens->init($appID, $appCertificate, $channelName, $userAccount);
        $privileges = AccessToken::PRIVILEGES;
        $token->addPrivilege($privileges["kJoinChannel"], $privilegeExpireTs);
        if(($role == RtcTokenBuilder::ROLEATTENDEE) ||
            ($role == RtcTokenBuilder::ROLEPUBLISHER) ||
            ($role == RtcTokenBuilder::ROLEADMIN))
        {
            $token->addPrivilege($privileges["kPublishVideoStream"], $privilegeExpireTs);
            $token->addPrivilege($privileges["kPublishAudioStream"], $privilegeExpireTs);
            $token->addPrivilege($privileges["kPublishDataStream"], $privilegeExpireTs);
        }
        return $token->build();
    }
}
