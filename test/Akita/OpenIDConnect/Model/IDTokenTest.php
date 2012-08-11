<?php

require_once dirname(__FILE__) . '/../../../../src/Akita/OpenIDConnect/Model/IDToken.php';

class Akita_OpenIDConnect_Model_IDToken_Test 
    extends PHPUnit_Framework_TestCase
{
    public function testIDTonenCreation()
    {
        $header = array(    "alg" => "none");
        $payload = array(   "iss" => "iss_url",
                            "user_id" => "user_id",
                            "aud" => "client_id",
                            "exp" => mktime(0, 0, 0, 8, 1, 2012),
                            "iat" => mktime(0, 0, 0, 7, 31, 2012)
                    );
        $dummy_key = "dummy key";
        // construct, setHeader, setPayload
        $idToken = new Akita_OpenIDConnect_Model_IDToken($header, $payload, $dummy_key);

        // getTokenString - Success
        $token_string = $idToken->getTokenString();
        $this->assertEquals('eyJhbGciOiJub25lIiwidHlwIjoiSldTIn0.eyJpc3MiOiJpc3NfdXJsIiwidXNlcl9pZCI6InVzZXJfaWQiLCJhdWQiOiJjbGllbnRfaWQiLCJleHAiOjEzNDM3NDY4MDAsImlhdCI6MTM0MzY2MDQwMH0.', $token_string);

        // setPayloadItem
        $idToken->setPayloadItem('ops','ops_string');
        $token_string = $idToken->getTokenString();
        $this->assertEquals('eyJhbGciOiJub25lIiwidHlwIjoiSldTIn0.eyJpc3MiOiJpc3NfdXJsIiwidXNlcl9pZCI6InVzZXJfaWQiLCJhdWQiOiJjbGllbnRfaWQiLCJleHAiOjEzNDM3NDY4MDAsImlhdCI6MTM0MzY2MDQwMCwib3BzIjoib3BzX3N0cmluZyJ9.', $token_string);

        // setHeaderItem
        $idToken->setHeaderItem('alg','HS256');
        $shared_key = 'This is shared key';
        $idToken->setKey($shared_key);
        $token_string = $idToken->getTokenString();
        $this->assertEquals('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJpc3MiOiJpc3NfdXJsIiwidXNlcl9pZCI6InVzZXJfaWQiLCJhdWQiOiJjbGllbnRfaWQiLCJleHAiOjEzNDM3NDY4MDAsImlhdCI6MTM0MzY2MDQwMCwib3BzIjoib3BzX3N0cmluZyJ9.larDD4dLtd65w5Ml8rjCO17wQ0AgQ7K6LgsQjf4ampw', $token_string);

        // setAccessTokenHash
        $idToken->setAccessTokenHash('access_token_string');
        $excepted_payload = array(
                                'iss' => 'iss_url',
                                'user_id' => 'user_id',
                                'aud' => 'client_id',
                                'exp' => 1343746800,
                                'iat' => 1343660400,
                                'ops' => 'ops_string',
                                'at_hash' => 'JnPXVfC--Wj6h3moc1dyiQ'
                            );
        $payload = $idToken->getPayload();
        $this->assertEquals($excepted_payload, $payload);

        // setCodeHash
        $idToken->setCodeHash('authorization_code_string');
        $excepted_payload = array(
                                'iss' => 'iss_url',
                                'user_id' => 'user_id',
                                'aud' => 'client_id',
                                'exp' => 1343746800,
                                'iat' => 1343660400,
                                'ops' => 'ops_string',
                                'at_hash' => 'JnPXVfC--Wj6h3moc1dyiQ',
                                'c_hash' => 'f0zfwRaKGf53ea5EmauamA'
                            );
        $payload = $idToken->getPayload();
        $this->assertEquals($excepted_payload, $payload);

        // TODO: getTokenString fail
        $idToken->setHeaderItem('alg','invalid');
        try {
            $idToken->setKey($dummy_key);
            $token_string = $idToken->getTokenString();
        }catch(Exception $e){
            $this->assertEquals('InvalidFormat', $e->getMessage());
        }
    }

    public function testIDTokenValidation()
    {
        $shared_key = 'This is shared key';
        $idTokenString = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJpc3MiOiJpc3NfdXJsIiwidXNlcl9pZCI6InVzZXJfaWQiLCJhdWQiOiJjbGllbnRfaWQiLCJleHAiOjEzNDM3NDY4MDAsImlhdCI6MTM0MzY2MDQwMCwib3BzIjoib3BzX3N0cmluZyJ9.larDD4dLtd65w5Ml8rjCO17wQ0AgQ7K6LgsQjf4ampw';
        $expected_header = array(   "alg" => "HS256", "typ" => "JWS");
        $expected_payload = array(   "iss" => "iss_url",
                            "user_id" => "user_id",
                            "aud" => "client_id",
                            "exp" => mktime(0, 0, 0, 8, 1, 2012),
                            "iat" => mktime(0, 0, 0, 7, 31, 2012),
                            "ops" => "ops_string"
                    );
        try {
            // load String success
            $idToken = Akita_OpenIDConnect_Model_IDToken::loadTokenString($idTokenString);
            // getHeader
            $header = $idToken->getHeader();
            $this->assertEquals($expected_header, $header);
            // getPayload
            $payload = $idToken->getPayload();
            $this->assertEquals($expected_payload, $payload);
            // validate
            $idToken->setKey($shared_key);
            $result = $idToken->validate();
            $this->assertEquals(true, $result);
        }catch(Exception $e){
            $this->assertEquals(false, true, $e->getMessage());
        }
    }
}
