<?php

require_once dirname(__FILE__) . '/../../../../../src/Akita/OpenIDConnect/Server/GrantHandler/AuthorizationCode.php';
require_once dirname(__FILE__) . '/../../../../../src/Akita/OpenIDConnect/Server/DataHandler.php';
require_once dirname(__FILE__) . '/../../../../../src/Akita/OpenIDConnect/Server/Request.php';
require_once 'Akita/OAuth2/Model/AccessToken.php';
require_once dirname(__FILE__) . '/../../../../../src/Akita/OpenIDConnect/Model/AuthInfo.php';
require_once dirname(__FILE__) . '/../../../../../src/Akita/OpenIDConnect/Model/IDToken.php';

// test class
class DataHandler_AuthorizationCode_Test 
    extends Akita_OpenIDConnect_Server_DataHandler
{
    private $_request;
    private $_authInfo;
    private $_accessToken;

    public function __construct($request, $authInfo, $accessToken){
        $this->_request = $request;
        $this->_authInfo = $authInfo;
        $this->_accessToken = $accessToken;
    }

    public function getRequest(){
        return $this->_request;
    }

    public function getUserId(){
        return null;
    }

    public function getUserIdByCredentials( $username, $password ){
        return null;
    }

    public function createOrUpdateAuthInfo( $params ){
        return null;
    }

    public function createOrUpdateAccessToken( $params ){
        return $this->_accessToken;
    }

    public function getAuthInfoByCode( $code ){
        if($code=='valid_code'){
            return $this->_authInfo;
        }else{
            return null;
        }
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
        if($clientId == 'valid_client_id' && $clientSecret == 'valid_client_secret'){
            return true;
        }else{
            return false;
        }
    }

    public function validateClientById( $clientId ){
        return false;
    }

    public function validateUserById( $userId ){
        return false;
    }

    public function validateRedirectUri( $clientId, $redirectUri){
        return false;
    }

    public function validateScope( $clientId, $scope ){
        return false;
    }

    public function validateScopeForTokenRefresh( $scope, $authInfo){
        return false;
    }

    public function setRefreshToken( $authInfo ){
        return $authInfo;
    }

    /**
     * check display param
     *
     * @param string $display display parameter
     * @return boolen
     */
    public function validateDisplay($display){
        return false;
    }

    /**
     * check prompt param
     *
     * @param array $prompt prompt parameter
     * @return boolen
     */
    public function validatePrompt($prompt){
        return false;
    }

    /**
     * check OpenID Request Object
     *
     * @param Akita_OpenID_Server_Request $request request object
     * @return boolen
     */
    public function validateRequestObject($request){
        return false;
    }

    /**
     * check ID Token
     *
     * @param array $prompt prompt parameter
     * @param string $id_token ID Token parameter
     * @return boolen
     */
    public function validateIDToken($prompt, $id_token){
        return false;
    }

    /**
     * create ID TOken for Authorization Response
     *
     * @return Akita_OpenIDConnect_Model_IDToken
     */
    public function createIdToken(){
        return "";
    }

}

class Akita_OpenIDConnect_Server_GrantHandler_AuthorizationCode_Test extends PHPUnit_Framework_TestCase
{
    public function testAuthorizationCode_success()
    {
        $server = array();
        $params = array(
            'code'=>'valid_code',
            'redirect_uri'=>'valid_redirect_uri',
            'client_id' => 'valid_client_id',
            'client_secret' => 'valid_client_secret'
        );
        $request = new Akita_OpenIDConnect_Server_Request('token', $server, $params);
        $authInfo = new Akita_OpenIDConnect_Model_AuthInfo();
        $authInfo->clientId = 'valid_client_id';
        $authInfo->redirectUri = 'valid_redirect_uri';
        $authInfo->refreshToken = 'test_refresh_token';
        $authInfo->idToken = 'test_id_token';
        $accessToken = new Akita_OAuth2_Model_AccessToken();
        $accessToken->token = 'test_access_token';
        $accessToken->expiresIn = 3600;
        $accessToken->scope = 'test_scope';
        $dataHandler = new DataHandler_AuthorizationCode_Test($request, $authInfo, $accessToken);
        $grantHandler = new Akita_OpenIDConnect_Server_GrantHandler_AuthorizationCode();
        try{
            $res = $grantHandler->handleRequest($dataHandler);
            $this->assertEquals('test_access_token', $res["access_token"], 'invalid response : access token');
            $this->assertEquals(3600, $res["expires_in"], 'invalid response : expires_in');
            $this->assertEquals('test_scope', $res["scope"], 'invalid response : scope');
            $this->assertEquals('test_refresh_token', $res["refresh_token"], 'invalid response : refresh token');
            $this->assertEquals('test_id_token', $res["id_token"], 'invalid response : id_token');
        }catch(Akita_OAuth2_Server_Error $error){
            $this->assertTrue(false, $error->getMessage());
        }
    }
}
