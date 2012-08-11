<?php

require_once dirname(__FILE__) . '/../../../../src/Akita/OpenIDConnect/Server/AuthorizationHandler.php';
require_once dirname(__FILE__) . '/../../../../src/Akita/OpenIDConnect/Server/DataHandler.php';
require_once dirname(__FILE__) . '/../../../../src/Akita/OpenIDConnect/Server/Request.php';
require_once 'Akita/OAuth2/Model/AccessToken.php';
require_once dirname(__FILE__) . '/../../../../src/Akita/OpenIDConnect/Model/AuthInfo.php';
require_once dirname(__FILE__) . '/../../../../src/Akita/OpenIDConnect/Model/IDToken.php';

// test class
class DataHandler_AuthorizationHandler_Test
    extends Akita_OpenIDConnect_Server_DataHandler
{
    private $_request;
    private $_authInfo;
    private $_accessToken;
    private $_idToken;

    private $_userId;

    public function __construct($request, $authInfo, $accessToken, $idToken=null){
        $this->_request = $request;
        $this->_authInfo = $authInfo;
        $this->_accessToken = $accessToken;
        $this->_idToken = $idToken;
    }

    public function setUserId($userId){
        $this->_userId = $userId;
    }

    /* abstruct functions */
    public function getRequest(){
        return $this->_request;
    }

    public function getUserId(){
        return $this->_userId;
    }

    public function getUserIdByCredentials( $username, $password ){
        return null;
    }

    public function createOrUpdateAuthInfo( $params ){
        return $this->_authInfo;
    }

    public function createOrUpdateAccessToken( $params ){
        return $this->_accessToken;
    }

    public function getAuthInfoByCode( $code ){
        return null;
    }

    public function getAuthInfoByRefreshToken( $refreshToken ){
            return null;
    }

    public function getAccessToken( $token ){
        return null;
    }

    public function getAuthInfoById( $authId ){
        return null;
    }

    public function validateClient( $clientId, $clientSecret, $grantType ){
        return false;
    }

    public function validateClientById( $clientId ){
        if($clientId == 'valid_client_id'){
            return true;
        }else{
            return false;
        }
    }

    public function validateUserById( $userId ){
        return false;
    }

    public function validateRedirectUri( $clientId, $redirectUri){
        if($clientId == 'valid_client_id' && $redirectUri == 'http://valid_redirect_uri/'){
            return true;
        }else{
            return false;
        }
    }

    public function validateScope( $clientId, $scope ){
        if($clientId == 'valid_client_id' && $scope == array('openid')){
            return true;
        }else{
            return false;
        }
    }

    public function validateScopeForTokenRefresh( $scope, $authInfo){
        return false;
    }

    public function setRefreshToken( $authInfo ){
        return null;
    }

    /**
     * check display param
     *
     * @param string $display display parameter
     * @return boolen
     */
    public function validateDisplay($display){
        if(!empty($display)){
            return ($display=='page');
        }else{
            return true;
        }
    }

    /**
     * check prompt param
     *
     * @param array $prompt prompt parameter
     * @return boolen
     */
    public function validatePrompt($prompt){
        $allowed_prompt = array('login');
        $allowed_prompt_none = array('none');
        if(!empty($prompt)){
            return ($allowed_prompt===$prompt || $allowed_prompt_none === $prompt);
        }else{
            return true;
        }
    }

    /**
     * check OpenID Request Object
     *
     * @param Akita_OpenID_Server_Request $request request object
     * @return boolen
     */
    public function validateRequestObject($request){
        return ($request->openidConnectRequest=='this_is_dummy_request');
    }

    /**
     * check ID Token
     *
     * @param array $prompt prompt parameter
     * @param string $id_token ID Token parameter
     * @return boolen
     */
    public function validateIDToken($prompt, $id_token){
        if($prompt==array('none') && empty($id_token)){
            return false;
        }else{
            return true;
        }
    }

    /**
     * create ID TOken for Authorization Response
     *
     * @return Akita_OpenIDConnect_Model_IDToken
     */
    public function createIdToken(){
        return $this->_idToken;
    }
}

class Akita_OAuth2_Server_AuthorizationHandler_Test extends PHPUnit_Framework_TestCase
{
    // processAuthorizationRequest
    // - validateNonce failed
    public function test_processAuthorizationRequest_nonce_required()
    {
        $server = array();
        $params = array(
            'response_type' => 'id_token',
            'client_id' => 'valid_client_id',
            'redirect_uri' => 'http://valid_redirect_uri/',
            'scope' => 'openid'
        );
        $request = new Akita_OpenIDConnect_Server_Request('authorization', $server, $params);
        $dataHandler = new DataHandler_AuthorizationHandler_Test($request, null, null);
        $authHandler = new Akita_OpenIDConnect_Server_AuthorizationHandler();
        try{
            $authHandler->processAuthorizationRequest($dataHandler);
        }catch(Akita_OAuth2_Server_Error $error){
            $this->assertEquals('400', $error->getOAuth2Code(), $error->getMessage());
            $this->assertEquals('invalid_request', $error->getOAuth2Error(), $error->getMessage());
            $this->assertEquals('nonce_required', $error->getOAuth2ErrorDescription(), $error->getMessage());
        }
    }

    // - validateDisplay failed
    public function test_processAuthorizationRequest_invalid_display()
    {
        $server = array();
        $params = array(
            'response_type' => 'id_token',
            'client_id' => 'valid_client_id',
            'redirect_uri' => 'http://valid_redirect_uri/',
            'scope' => 'openid',
            'nonce' => 'nonce',
            'display' => 'popup'
        );
        $request = new Akita_OpenIDConnect_Server_Request('authorization', $server, $params);
        $dataHandler = new DataHandler_AuthorizationHandler_Test($request, null, null);
        $authHandler = new Akita_OpenIDConnect_Server_AuthorizationHandler();
        try{
            $authHandler->processAuthorizationRequest($dataHandler);
        }catch(Akita_OAuth2_Server_Error $error){
            $this->assertEquals('400', $error->getOAuth2Code(), $error->getMessage());
            $this->assertEquals('invalid_request', $error->getOAuth2Error(), $error->getMessage());
            $this->assertEquals("'display' is invalid", $error->getOAuth2ErrorDescription(), $error->getMessage());
        }
    }

    // - validatePrompt failed
    public function test_processAuthorizationRequest_invalid_prompt()
    {
        $server = array();
        $params = array(
            'response_type' => 'id_token',
            'client_id' => 'valid_client_id',
            'redirect_uri' => 'http://valid_redirect_uri/',
            'scope' => 'openid',
            'nonce' => 'nonce',
            'display' => 'page',
            'prompt' => 'invalid'
        );
        $request = new Akita_OpenIDConnect_Server_Request('authorization', $server, $params);
        $dataHandler = new DataHandler_AuthorizationHandler_Test($request, null, null);
        $authHandler = new Akita_OpenIDConnect_Server_AuthorizationHandler();
        try{
            $authHandler->processAuthorizationRequest($dataHandler);
        }catch(Akita_OAuth2_Server_Error $error){
            $this->assertEquals('400', $error->getOAuth2Code(), $error->getMessage());
            $this->assertEquals('invalid_request', $error->getOAuth2Error(), $error->getMessage());
            $this->assertEquals("'prompt' is invalid", $error->getOAuth2ErrorDescription(), $error->getMessage());
        }
    }

    // - validateRequest failed
    public function test_processAuthorizationRequest_invalid_request()
    {
        $server = array();
        $params = array(
            'response_type' => 'id_token',
            'client_id' => 'valid_client_id',
            'redirect_uri' => 'http://valid_redirect_uri/',
            'scope' => 'openid',
            'nonce' => 'nonce',
            'display' => 'page',
            'prompt' => 'login',
            'request' => 'invalid_request'
        );
        $request = new Akita_OpenIDConnect_Server_Request('authorization', $server, $params);
        $dataHandler = new DataHandler_AuthorizationHandler_Test($request, null, null);
        $authHandler = new Akita_OpenIDConnect_Server_AuthorizationHandler();
        try{
            $authHandler->processAuthorizationRequest($dataHandler);
        }catch(Akita_OAuth2_Server_Error $error){
            $this->assertEquals('400', $error->getOAuth2Code(), $error->getMessage());
            $this->assertEquals('invalid_request', $error->getOAuth2Error(), $error->getMessage());
            $this->assertEquals("'request' is invalid", $error->getOAuth2ErrorDescription(), $error->getMessage());
        }
    }

    // - validateRequestUri failed
    public function test_processAuthorizationRequest_invalid_request_uri()
    {
        $server = array();
        $params = array(
            'response_type' => 'id_token',
            'client_id' => 'valid_client_id',
            'redirect_uri' => 'http://valid_redirect_uri/',
            'scope' => 'openid',
            'nonce' => 'nonce',
            'display' => 'page',
            'prompt' => 'login',
            'request_uri' => 'https://openidconnect.info/images/dummyrequest.txt'
        );
        $request = new Akita_OpenIDConnect_Server_Request('authorization', $server, $params);
        $dataHandler = new DataHandler_AuthorizationHandler_Test($request, null, null);
        $authHandler = new Akita_OpenIDConnect_Server_AuthorizationHandler();
        try{
            $authHandler->processAuthorizationRequest($dataHandler);
        }catch(Akita_OAuth2_Server_Error $error){
            $this->assertEquals('400', $error->getOAuth2Code(), $error->getMessage());
            $this->assertEquals('invalid_request', $error->getOAuth2Error(), $error->getMessage());
            $this->assertEquals("'request_uri' is invalid", $error->getOAuth2ErrorDescription(), $error->getMessage());
        }
    }

    // - validateRequestUri failed
    public function test_processAuthorizationRequest_invalid_id_token()
    {
        $server = array();
        $params = array(
            'response_type' => 'id_token',
            'client_id' => 'valid_client_id',
            'redirect_uri' => 'http://valid_redirect_uri/',
            'scope' => 'openid',
            'nonce' => 'nonce',
            'display' => 'page',
            'prompt' => 'none',
            'id_token' => ''
        );
        $request = new Akita_OpenIDConnect_Server_Request('authorization', $server, $params);
        $dataHandler = new DataHandler_AuthorizationHandler_Test($request, null, null);
        $authHandler = new Akita_OpenIDConnect_Server_AuthorizationHandler();
        try{
            $authHandler->processAuthorizationRequest($dataHandler);
        }catch(Akita_OAuth2_Server_Error $error){
            $this->assertEquals('400', $error->getOAuth2Code(), $error->getMessage());
            $this->assertEquals('interaction_required', $error->getOAuth2Error(), $error->getMessage());
            $this->assertEmpty($error->getOAuth2ErrorDescription(), $error->getMessage());
        }
    }

    // allowAuthorizationRequest
    // - createIDToken failed
    public function test_allowAuthorizationRequest_token_id_token_failed()
    {
        $server = array();
        $params = array(
            'response_type' => 'id_token token',
            'client_id' => 'valid_client_id',
            'redirect_uri' => 'http://valid_redirect_uri/',
            'scope' => 'openid',
            'nonce' => 'nonce'
        );
        $request = new Akita_OpenIDConnect_Server_Request('authorization', $server, $params);
        $authInfo = new Akita_OpenIDConnect_Model_AuthInfo();
        $authInfo->code = 'test_code';
        $accessToken = new Akita_OAuth2_Model_AccessToken();
        $accessToken->token = 'test_access_token';
        $accessToken->expiresIn = 3600;
        $accessToken->scope = 'test_scope';
        $dataHandler = new DataHandler_AuthorizationHandler_Test($request, $authInfo, $accessToken);
        $authHandler = new Akita_OpenIDConnect_Server_AuthorizationHandler();
        try{
            $res = $authHandler->allowAuthorizationRequest($dataHandler);
        }catch(Akita_OAuth2_Server_Error $error){
            $this->assertEquals('500', $error->getOAuth2Code(), $error->getMessage());
            $this->assertEquals('server_error', $error->getOAuth2Error(), $error->getMessage());
            $this->assertEmpty($error->getOAuth2ErrorDescription(), $error->getMessage());
        }
    }

    // - success : id_token
    public function test_allowAuthorizationRequest_id_token_success()
    {
        $server = array();
        $params = array(
            'response_type' => 'id_token',
            'client_id' => 'valid_client_id',
            'redirect_uri' => 'http://valid_redirect_uri/',
            'scope' => 'openid',
            'nonce' => 'nonce'
        );
        $request = new Akita_OpenIDConnect_Server_Request('authorization', $server, $params);
        $authInfo = new Akita_OpenIDConnect_Model_AuthInfo();
        $authInfo->code = 'test_code';
        $accessToken = new Akita_OAuth2_Model_AccessToken();
        $accessToken->token = 'test_access_token';
        $accessToken->expiresIn = 3600;
        $accessToken->scope = 'test_scope';
        $idToken_header = array('alg' => 'none');
        $idToken_payload = array('dummy', 'dummy');
        $idToken = new Akita_OpenIDConnect_Model_IDToken($idToken_header, $idToken_payload);
        $dataHandler = new DataHandler_AuthorizationHandler_Test($request, $authInfo, $accessToken, $idToken);
        $authHandler = new Akita_OpenIDConnect_Server_AuthorizationHandler();
        try{
            $res = $authHandler->allowAuthorizationRequest($dataHandler);
            /*
                array(3) {
                    ["redirect_uri"]=>
                    string(26) "http://valid_redirect_uri/"
                    ["query"]=>
                    array(0) {
                    }
                    ["fragment"]=>
                    array(1) {
                        ["id_token"]=>
                        string(60) "eyJhbGciOiJub25lIiwidHlwIjoiSldTIn0.WyJkdW1teSIsImR1bW15Il0."
                    }
                }
            */
            $this->assertEquals('http://valid_redirect_uri/', $res['redirect_uri']);
            $this->assertEmpty(@$res['query']);
            $this->assertEquals('eyJhbGciOiJub25lIiwidHlwIjoiSldTIn0.WyJkdW1teSIsImR1bW15Il0.', $res['fragment']['id_token']);
            $this->assertEmpty(@$res['fragment']['access_token']);
            $this->assertEmpty(@$res['fragment']['code']);
            $this->assertEmpty(@$res['fragment']['state']);
            $this->assertEmpty(@$res['fragment']['expires_in']);
            $this->assertEmpty(@$res['fragment']['scope']);
            // TODO: ID Token validate (c_hash and at_hash does not exist)
        }catch(Akita_OAuth2_Server_Error $error){
            $this->assertTrue(false, $error->getMessage());
        }
    }

    // - success : code id_token
    public function test_allowAuthorizationRequest_code_id_token_success()
    {
        $server = array();
        $params = array(
            'response_type' => 'code id_token',
            'client_id' => 'valid_client_id',
            'redirect_uri' => 'http://valid_redirect_uri/',
            'scope' => 'openid',
            'nonce' => 'nonce'
        );
        $request = new Akita_OpenIDConnect_Server_Request('authorization', $server, $params);
        $authInfo = new Akita_OpenIDConnect_Model_AuthInfo();
        $authInfo->code = 'test_code';
        $accessToken = new Akita_OAuth2_Model_AccessToken();
        $accessToken->token = 'test_access_token';
        $accessToken->expiresIn = 3600;
        $accessToken->scope = 'test_scope';
        $idToken_header = array('alg' => 'none');
        $idToken_payload = array('dummy', 'dummy');
        $idToken = new Akita_OpenIDConnect_Model_IDToken($idToken_header, $idToken_payload);
        $dataHandler = new DataHandler_AuthorizationHandler_Test($request, $authInfo, $accessToken, $idToken);
        $authHandler = new Akita_OpenIDConnect_Server_AuthorizationHandler();
        try{
            $res = $authHandler->allowAuthorizationRequest($dataHandler);
            /*
                array(3) {
                    ["redirect_uri"]=>
                    string(26) "http://valid_redirect_uri/"
                    ["query"]=>
                    array(0) {
                    }
                    ["fragment"]=>
                    array(2) {
                        ["code"]=>
                        string(9) "test_code"
                        ["id_token"]=>
                        string(116) "eyJhbGciOiJub25lIiwidHlwIjoiSldTIn0.eyIwIjoiZHVtbXkiLCIxIjoiZHVtbXkiLCJjX2hhc2giOiJPcUFFRjRIMVAxa1FpZm9xN3RBVElBIn0."
                    }
                }
            */
            $this->assertEquals('http://valid_redirect_uri/', $res['redirect_uri']);
            $this->assertEmpty(@$res['query']);
            $this->assertEquals('eyJhbGciOiJub25lIiwidHlwIjoiSldTIn0.eyIwIjoiZHVtbXkiLCIxIjoiZHVtbXkiLCJjX2hhc2giOiJPcUFFRjRIMVAxa1FpZm9xN3RBVElBIn0.', $res['fragment']['id_token']);
            $this->assertEquals('test_code', $res['fragment']['code']);
            $this->assertEmpty(@$res['fragment']['access_token']);
            $this->assertEmpty(@$res['fragment']['state']);
            $this->assertEmpty(@$res['fragment']['expires_in']);
            $this->assertEmpty(@$res['fragment']['scope']);
            // TODO: ID Token validate (c_hash)
        }catch(Akita_OAuth2_Server_Error $error){
            $this->assertTrue(false, $error->getMessage());
        }
    }

    // - success : id_token token
    public function test_allowAuthorizationRequest_id_token_token_success()
    {
        $server = array();
        $params = array(
            'response_type' => 'id_token token',
            'client_id' => 'valid_client_id',
            'redirect_uri' => 'http://valid_redirect_uri/',
            'scope' => 'openid',
            'nonce' => 'nonce'
        );
        $request = new Akita_OpenIDConnect_Server_Request('authorization', $server, $params);
        $authInfo = new Akita_OpenIDConnect_Model_AuthInfo();
        $authInfo->code = 'test_code';
        $accessToken = new Akita_OAuth2_Model_AccessToken();
        $accessToken->token = 'test_access_token';
        $accessToken->expiresIn = 3600;
        $accessToken->scope = 'test_scope';
        $idToken_header = array('alg' => 'none');
        $idToken_payload = array('dummy', 'dummy');
        $idToken = new Akita_OpenIDConnect_Model_IDToken($idToken_header, $idToken_payload);
        $dataHandler = new DataHandler_AuthorizationHandler_Test($request, $authInfo, $accessToken, $idToken);
        $authHandler = new Akita_OpenIDConnect_Server_AuthorizationHandler();
        try{
            $res = $authHandler->allowAuthorizationRequest($dataHandler);
            /*
                array(3) {
                    ["redirect_uri"]=>
                    string(26) "http://valid_redirect_uri/"
                    ["query"]=>
                    array(0) {
                    }
                    ["fragment"]=>
                    array(5) {
                        ["access_token"]=>
                        string(17) "test_access_token"
                        ["expires_in"]=>
                        int(3600)
                        ["scope"]=>
                        string(10) "test_scope"
                        ["token_type"]=>
                        string(6) "Bearer"
                        ["id_token"]=>
                        string(117) "eyJhbGciOiJub25lIiwidHlwIjoiSldTIn0.eyIwIjoiZHVtbXkiLCIxIjoiZHVtbXkiLCJhdF9oYXNoIjoiU1pOVkx5ekd4T1ZfcFhPUG14WWFHZyJ9."
                    }
                }
            */
            $this->assertEquals('http://valid_redirect_uri/', $res['redirect_uri']);
            $this->assertEmpty(@$res['query']);
            $this->assertEquals('eyJhbGciOiJub25lIiwidHlwIjoiSldTIn0.eyIwIjoiZHVtbXkiLCIxIjoiZHVtbXkiLCJhdF9oYXNoIjoiU1pOVkx5ekd4T1ZfcFhPUG14WWFHZyJ9.', $res['fragment']['id_token']);
            $this->assertEquals('test_access_token', $res['fragment']['access_token']);
            $this->assertEquals(3600, $res['fragment']['expires_in']);
            $this->assertEquals('test_scope', $res['fragment']['scope']);
            $this->assertEquals('Bearer', $res['fragment']['token_type']);
            $this->assertEmpty(@$res['fragment']['code']);
            $this->assertEmpty(@$res['fragment']['state']);
            // TODO: ID Token validate (at_hash)
        }catch(Akita_OAuth2_Server_Error $error){
            $this->assertTrue(false, $error->getMessage());
        }
    }

    // - success : code id_token token
    public function test_allowAuthorizationRequest_code_id_token_token_success()
    {
        $server = array();
        $params = array(
            'response_type' => 'code id_token token',
            'client_id' => 'valid_client_id',
            'redirect_uri' => 'http://valid_redirect_uri/',
            'scope' => 'openid',
            'nonce' => 'nonce'
        );
        $request = new Akita_OpenIDConnect_Server_Request('authorization', $server, $params);
        $authInfo = new Akita_OpenIDConnect_Model_AuthInfo();
        $authInfo->code = 'test_code';
        $accessToken = new Akita_OAuth2_Model_AccessToken();
        $accessToken->token = 'test_access_token';
        $accessToken->expiresIn = 3600;
        $accessToken->scope = 'test_scope';
        $idToken_header = array('alg' => 'none');
        $idToken_payload = array('dummy', 'dummy');
        $idToken = new Akita_OpenIDConnect_Model_IDToken($idToken_header, $idToken_payload);
        $dataHandler = new DataHandler_AuthorizationHandler_Test($request, $authInfo, $accessToken, $idToken);
        $authHandler = new Akita_OpenIDConnect_Server_AuthorizationHandler();
        try{
            $res = $authHandler->allowAuthorizationRequest($dataHandler);
            /*
                array(3) {
                    ["redirect_uri"]=>
                    string(26) "http://valid_redirect_uri/"
                    ["query"]=>
                    array(0) {
                    }
                    ["fragment"]=>
                    array(6) {
                        ["access_token"]=>
                        string(17) "test_access_token"
                        ["expires_in"]=>
                        int(3600)
                        ["scope"]=>
                        string(10) "test_scope"
                        ["token_type"]=>
                        string(6) "Bearer"
                        ["code"]=>
                        string(9) "test_code"
                        ["id_token"]=>
                        string(163) "eyJhbGciOiJub25lIiwidHlwIjoiSldTIn0.eyIwIjoiZHVtbXkiLCIxIjoiZHVtbXkiLCJhdF9oYXNoIjoiU1pOVkx5ekd4T1ZfcFhPUG14WWFHZyIsImNfaGFzaCI6Ik9xQUVGNEgxUDFrUWlmb3E3dEFUSUEifQ."
                    }
                }
            */
            $this->assertEquals('http://valid_redirect_uri/', $res['redirect_uri']);
            $this->assertEmpty(@$res['query']);
            $this->assertEquals('eyJhbGciOiJub25lIiwidHlwIjoiSldTIn0.eyIwIjoiZHVtbXkiLCIxIjoiZHVtbXkiLCJhdF9oYXNoIjoiU1pOVkx5ekd4T1ZfcFhPUG14WWFHZyIsImNfaGFzaCI6Ik9xQUVGNEgxUDFrUWlmb3E3dEFUSUEifQ.', $res['fragment']['id_token']);
            $this->assertEquals('test_access_token', $res['fragment']['access_token']);
            $this->assertEquals(3600, $res['fragment']['expires_in']);
            $this->assertEquals('test_scope', $res['fragment']['scope']);
            $this->assertEquals('Bearer', $res['fragment']['token_type']);
            $this->assertEquals('test_code', $res['fragment']['code']);
            $this->assertEmpty(@$res['fragment']['state']);
            // TODO: ID Token validate (at_hash)
        }catch(Akita_OAuth2_Server_Error $error){
            $this->assertTrue(false, $error->getMessage());
        }
    }
}
