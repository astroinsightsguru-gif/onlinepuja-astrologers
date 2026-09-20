<?php

namespace TaylanUnutmaz\AgoraTokenBuilder;
namespace App\AgoraToken;
class RtmTokenBuilder
{
    const ROLERTMUSER = 1;
    public function buildToken($appID, $appCertificate, $userAccount, $privilegeExpireTs){
        $tokens = new AccessToken();
        $token = $tokens->init($appID, $appCertificate, $userAccount, "");
        $privileges= AccessToken::PRIVILEGES;
        $token->addPrivilege($privileges["kRtmLogin"], $privilegeExpireTs);
        return $token->build();
    }
}
