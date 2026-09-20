<?php

namespace TaylanUnutmaz\AgoraTokenBuilder;
namespace App\AgoraToken;
class RtmTokenBuilder2
{
    const ROLERTMUSER = 1;
    public static function buildToken($appId, $appCertificate, $userId, $expire)
    {
        $accessToken = new AccessToken2($appId, $appCertificate, $expire);
        $serviceRtm = new ServiceRtm($userId);

        $serviceRtm->addPrivilege($serviceRtm::PRIVILEGE_LOGIN, $expire);
        $accessToken->addService($serviceRtm);

        return $accessToken->build();
    }
}
