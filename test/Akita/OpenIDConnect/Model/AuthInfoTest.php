<?php

require_once dirname(__FILE__) . '/../../../../src/Akita/OpenIDConnect/Model/AuthInfo.php';

class Akita_OpenIDConnect_Model_AuthInfo_Test extends PHPUnit_Framework_TestCase
{
    public function testAccessor()
    {
        $authinfo = new Akita_OpenIDConnect_Model_AuthInfo();
        $this->assertClassHasAttribute('authId','Akita_OpenIDConnect_Model_AuthInfo');
        $this->assertClassHasAttribute('userId','Akita_OpenIDConnect_Model_AuthInfo');
        $this->assertClassHasAttribute('clientId','Akita_OpenIDConnect_Model_AuthInfo');
        $this->assertClassHasAttribute('scope','Akita_OpenIDConnect_Model_AuthInfo');
        $this->assertClassHasAttribute('refreshToken','Akita_OpenIDConnect_Model_AuthInfo');
        $this->assertClassHasAttribute('code','Akita_OpenIDConnect_Model_AuthInfo');
        $this->assertClassHasAttribute('redirectUri','Akita_OpenIDConnect_Model_AuthInfo');
        $this->assertClassHasAttribute('idToken','Akita_OpenIDConnect_Model_AuthInfo');

        $this->assertEmpty($authinfo->idToken);
        $authinfo->idToken = 'id_token';
        $this->assertEquals('id_token', $authinfo->idToken);
        $authinfo->idToken = '';
        $this->assertEmpty($authinfo->idToken);
    }
}
