<?php

require_once dirname(__FILE__) . '/../../../../src/Akita/OpenIDConnect/Server/Request.php';

class Akita_OpenIDConnect_Server_Request_Test extends PHPUnit_Framework_TestCase
{
    public function testSetRequestObject()
    {
        // request_uri
        $server = array();
        $params = array(
                        'request_uri' => 'https://openidconnect.info/images/dummyrequest.txt'
                        );
        $httpClient = new Akita_OpenIDConnect_Util_HttpClient();
        $httpClient->setTimeout(20,20);
        //$httpClient->setSslVerify(false, false);
        $request = new Akita_OpenIDConnect_Server_Request('authorization', $server, $params, array(), $httpClient);
        $this->assertEquals('this_is_dummy_request', $request->openidConnectRequest, 'HTTP Request to request_uri is failed');

        // request
        $params = array(
                        'request' => 'this_is_dummy_request'
                        );
        $request = new Akita_OpenIDConnect_Server_Request('authorization', $server, $params);
        $this->assertEquals('this_is_dummy_request', $request->openidConnectRequest, 'Request setter failed');
    }

    public function testSetScope()
    {
        $server = array ();
        $params = array(
                        'scope' => 'openid'
                        );
        $request = new Akita_OpenIDConnect_Server_Request('authorization', $server, $params);
        $this->assertEquals(array('openid'), $request->openidConnectScope, 'Scope setter failed');
        $params = array(
                        'scope' => 'openid profile email address phone'
                        );
        $request = new Akita_OpenIDConnect_Server_Request('authorization', $server, $params);
        $this->assertEquals(array('openid', 'profile', 'email', 'address', 'phone'), $request->openidConnectScope, 'Scope setter failed');
    }

    public function testSetResponseType()
    {
        $server = array ();
        $params = array(
                        'response_type' => 'code'
                        );
        $request = new Akita_OpenIDConnect_Server_Request('authorization', $server, $params);
        $this->assertEquals('code', $request->openidConnectResponseType, 'Response Type setter failed');
        $params = array(
                        'response_type' => 'token code id_token'
                        );
        $request = new Akita_OpenIDConnect_Server_Request('authorization', $server, $params);
        $this->assertEquals('code id_token token', $request->openidConnectResponseType, 'Response Type setter failed');
    }

    public function testSetPrompt()
    {
        $server = array ();
        $params = array(
                        'prompt' => 'none'
                        );
        $request = new Akita_OpenIDConnect_Server_Request('authorization', $server, $params);
        $this->assertEquals(array('none'), $request->openidConnectPrompt, 'Prompt setter failed');
        $params = array(
                        'prompt' => 'login consent'
                        );
        $request = new Akita_OpenIDConnect_Server_Request('authorization', $server, $params);
        $this->assertEquals(array('login', 'consent'), $request->openidConnectPrompt, 'Prompt setter failed');
    }
}
