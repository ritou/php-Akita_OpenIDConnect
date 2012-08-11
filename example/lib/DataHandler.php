<?php

require_once '../src/Akita/OpenIDConnect.php';
require_once './lib/DB.php';

class Akita_OpenIDConnect_Server_Sample_DataHandler
    extends Akita_OpenIDConnect_Server_DataHandler
{
    private $_request;
    private $_userId;
    private $_db;

    public function __construct($request){
        $this->_request = $request;
        $this->_db = new Akita_OpenIDConnect_Server_Sample_DB();
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
        if($username=='fakeuser@example.com' && $password=='fakepassword'){
            return $username;
        }else{
            return null;
        }
    }

    public function createOrUpdateAuthInfo( $params ){
        $authInfo = $this->_db->getAuthInfo($params['clientId'], $params['userId'], $params['scope']);
        if(is_null($authInfo)){
            $authId = hash_hmac('sha256','ai'.microtime(true).mt_rand(),$params['clientId'].$params['userId']);
            $authInfo = new Akita_OpenIDConnect_Model_AuthInfo(
                                $authId,
                                $params['userId'],
                                $params['clientId'],
                                $params['scope']
            );
        }

        // optional member
        if(isset($this->_request->param['response_type'])){
            if(strpos($this->_request->param['response_type'],'code') !== false){
                $authInfo->code = hash_hmac('sha256','cd'.microtime(true).mt_rand(),$params['clientId'].$params['userId']);
                $authInfo->redirectUri = $this->_request->param['redirect_uri'];
            }
            //$authInfo->refreshToken = hash_hmac('sha256','rt'.microtime(true).mt_rand(),$params['clientId'].$params['userId']);
        }
        $exp = time() + 600;
        // id_token
        $authInfo->idToken = $params['idToken']->getTokenString();
        // set UserClaims
        $authInfo->userInfoClaims = array('user_id', 'name', 'family_name', 'given_name', 'middle_name', 'nickname');
        $this->_db->setAuthInfo($authInfo, $exp);
        return $authInfo;
    }

    public function createOrUpdateAccessToken( $params ){
        if(empty($scope)){
               $scope = $params['authInfo']->scope;
        }
        $expiresIn = 3600;
        $createdOn = time();
        $token = hash_hmac('sha256', 'at'.microtime(true).mt_rand(),$params['authInfo']->clientId.$params['authInfo']->userId);

        $accessToken = new Akita_OAuth2_Model_AccessToken(
                                                           $params['authInfo']->authId,
                                                           $token,
                                                           $scope,
                                                           $expiresIn,
                                                           $createdOn);

        $this->_db->setAccessToken($accessToken);
        return $accessToken;
    }

    public function getAuthInfoByCode( $code ){
        $authInfo = $this->_db->getAuthInfoByCode($code);
        if(!is_null($authInfo)){
            $exp = time() + 14*24*60*60;
            $this->_db->setAuthInfo($authInfo, $exp, 1);
        }
        return $authInfo;
    }

    public function getAuthInfoByRefreshToken( $refreshToken ){
        return $this->_db->getAuthInfoByRefreshToken($refreshToken);
    }

    public function getAccessToken( $token ){
        return $this->_db->getAccessTokenByToken($token);
    }

    public function getAuthInfoById( $authId ){
        return $this->_db->getAuthInfoByAuthId($authId);
    }

    public function validateClient( $clientId, $clientSecret, $grantType ){
        if($clientId=='cid00001' && $clientSecret=='csecret00001'){
            return true;
        }else{
            return false;
        }
    }

    public function validateClientById( $clientId ){
        if($clientId=='cid00001'){
            return true;
        }else{
            return false;
        }
    }

    public function validateUserById( $userId ){
        if($userId=='fakeuser@example.com'){
            return true;
        }else{
            return false;
        }
    }

    public function validateRedirectUri( $clientId, $redirectUri){
        
        $valid_redirectUri = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];
        if(strrpos($valid_redirectUri, '/Authorization.php') ){
            $valid_redirectUri = str_replace('Authorization.php','Client.php',$valid_redirectUri);
        }else{
            $valid_redirectUri = str_replace('Finish.php','Client.php',$valid_redirectUri);
        }

        if($clientId=='cid00001' && $redirectUri==$valid_redirectUri){
            return true;
        }else{
            return false;
        }
    }

    public function validateScope( $clientId, $scope ){
        if($clientId=='cid00001' && ($scope==array('openid', 'profile'))){
            return true;
        }else{
            return false;
        }
    }

    public function validateScopeForTokenRefresh( $scope, $authInfo){
        if(strpos($authInfo->scope, $scope)!==false){
            return true;
        }else{
            return false;
        }
    }

    public function setRefreshToken( $authInfo ){
        $authInfo->code = "";
        $authInfo->refreshToken = hash_hmac('sha256','rt'.microtime(true).mt_rand(),$params['clientId'].$params['userId']);
        $exp = time() + 600;
        $this->_db->setAuthInfo($authInfo, $exp);
        return $authInfo;
    }

    /**
     * check display param
     *
     * @param string $display display parameter
     * @return boolen
     */
    public function validateDisplay($display){
        return true;
    }

    /**
     * check prompt param
     *
     * @param array $prompt prompt parameter
     * @return boolen
     */
    public function validatePrompt($prompt){
        return true;
    }

    /**
     * check OpenID Request Object
     *
     * @param Akita_OpenID_Server_Request $request request object
     * @return boolen
     */
    public function validateRequestObject($request){
        return true;
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
        $header = array("alg" => "HS256");
        $payload = array(
                    "iss" => "https://op.example.com",
                    "user_id" => "user_id",
                    "aud" => "cid00001",
                    "exp" => time() + 3600,
                    "iat" => time(),
        );
        $key = "dummy_key";
        $id_token = new Akita_OpenIDConnect_Model_IDToken($header, $payload, $key);
        return $id_token;
    }
}
