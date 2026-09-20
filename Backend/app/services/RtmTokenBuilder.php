<?php
namespace App\Services;

use App\services\AccessToken;


class RtmTokenBuilder
{
    const ROLERTMUSER = 1;

    public static function buildToken($appID, $appCertificate, $userAccount, $privilegeExpireTs)
    {
        $token = AccessToken::init($appID, $appCertificate, $userAccount, "");
        $privileges = AccessToken::PRIVILEGES;
        $token->addPrivilege($privileges["kRtmLogin"], $privilegeExpireTs);
        return $token->build();
    }
}
