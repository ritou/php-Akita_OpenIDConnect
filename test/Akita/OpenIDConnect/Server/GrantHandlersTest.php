<?php

require_once dirname(__FILE__) . '/../../../../src/Akita/OpenIDConnect/Server/GrantHandlers.php';


class Akita_OpenIDConnect_Server_GrantHandlers_Test extends PHPUnit_Framework_TestCase
{
    public function testInstance()
    {
        $grantHandler = Akita_OpenIDConnect_Server_GrantHandlers::getHandler('authorization_code');
        $this->assertInstanceOf('Akita_OpenIDConnect_Server_GrantHandler_AuthorizationCode',$grantHandler);
        $grantHandler = Akita_OpenIDConnect_Server_GrantHandlers::getHandler('refresh_token');
        $this->assertInstanceOf('Akita_OAuth2_Server_GrantHandler_RefreshToken',$grantHandler);
        $grantHandler = Akita_OpenIDConnect_Server_GrantHandlers::getHandler('client_credentials');
        $this->assertInstanceOf('Akita_OAuth2_Server_GrantHandler_ClientCredentials',$grantHandler);
        $grantHandler = Akita_OpenIDConnect_Server_GrantHandlers::getHandler('password');
        $this->assertInstanceOf('Akita_OAuth2_Server_GrantHandler_Password',$grantHandler);
        try{
            $grantHandler = Akita_OpenIDConnect_Server_GrantHandlers::getHandler('invalid_grant');
        }catch(Akita_OAuth2_Server_Error $error){
            $this->assertEquals('400', $error->getOAuth2Code(), $error->getMessage());
            $this->assertEquals('unsupported_grant_type', $error->getOAuth2Error(), $error->getMessage());
            $this->assertEmpty($error->getOAuth2ErrorDescription(), $error->getMessage());
        }
    }
}
