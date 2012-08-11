<?php
/**
 * Akita_OpenIDConnect_Server_DataHandler 
 *
 * datahandler abstract class
 *
 * PHP versions 5
 *
 * LICENSE: MIT License
 *
 * @category  OpenIDConnect
 * @package   Akita_OpenIDConnect
 * @author    Ryo Ito <ritou.06@gmail.com>
 * @copyright 2012 Ryo Ito
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @link      http://
 */

/**
 * Akita_OpenIDConnect_Server_DataHandler
 *
 * abstrct class
 *
 * @category  OpenIDConnect
 * @package   Akita_OpenIDConnect
 * @author    Ryo Ito <ritou.06@gmail.com>
 * @copyright 2012 Ryo Ito
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @link      http://
 */
abstract class Akita_OpenIDConnect_Server_DataHandler
{

    /**
     * return request object
     *
     * @return Akita_OAuth2_Server_Request object
     */
    abstract public function getRequest();

    /**
     * return user_id from request
     *
     * @return string user identifier or null
     */
    abstract public function getUserId();

    /**
     * validate username(or email)/password and return user_id
     *
     * @param string $username User's Name or Email
     * @param string $password User's Password
     * @return string user identifier or null
     */
    abstract public function getUserIdByCredentials( $username, $password );

    /**
     * create or update AuthInfo
     *
     * @param array $params params for generate AuthInfo
     * @return Akita_OAuth2_Model_Authinfo object
     */
    abstract public function createOrUpdateAuthInfo( $params );

    /**
     * create or update AccessToken
     *
     * @param array $params params for issue new Access Toen
     * @return Akita_OAuth2_Model_AccessToken object
     */
    abstract public function createOrUpdateAccessToken( $params );

    /**
     * validate Authorization Code and return related AuthInfo
     *
     * @param string $code Authrization Code
     * @return Akita_OAuth2_Model_Authinfo object
     */
    abstract public function getAuthInfoByCode( $code );

    /**
     * validate Authorization Code and return related AuthInfo
     *
     * @param string $refreshToken Refresh Token
     * @return Akita_OAuth2_Model_Authinfo object
     */
    abstract public function getAuthInfoByRefreshToken( $refreshToken );

    /**
     * validate Access Token Scting and return Access Token object
     *
     * @param string $token Access Token String in HTTP Request
     * @return Akita_OAuth2_Model_AccessToken object
     */
    abstract public function getAccessToken( $token );

    /**
     * get AuthInfo object from auth_id which is member of Access Token object
     *
     * @param string $authId Identifier of AuthInfo
     * @return Akita_OAuth2_Model_Authinfo object
     */
    abstract public function getAuthInfoById( $authId );

    /**
     * validate client crdential and grant type
     *
     * @param string $clientId Client ID
     * @param string $clientSecret Client Secret 
     * @param string $grantType Grant Type 
     * @return boolean vavalidation result
     */
    abstract public function validateClient( $clientId, $clientSecret, $grantType );

    /**
     * check client
     *
     * @param string $clientId Client Identifier of AuthInfo
     * @return boolen
     */
    abstract public function validateClientById( $clientId );

    /**
     * check user
     *
     * @param string $userId User Identifier of AuthInfo
     * @return boolen
     */
    abstract public function validateUserById( $userId );

    /**
     * check redirect_uri and Client ID
     *
     * @param string $clientId Client ID
     * @param string $redirect_uri redirect_uri Parameter
     * @return boolen
     */
    abstract public function validateRedirectUri( $clientId, $redirectUri );

    /**
     * check scope and Client ID
     *
     * @param string $clientId Client ID
     * @param string $scope Sope parameter
     * @return boolen
     */
    abstract public function validateScope( $clientId, $scope );

    /**
     * check scope and AuthInfo
     *
     * @param string $scope Sope parameter
     * @param Akita_OAuth2_Model_Authinfo $authInfo AuthInfo
     * @return boolen
     */
    abstract public function validateScopeForTokenRefresh( $scope, $authInfo);

    /**
     * remove Authorization Code from AuthInfo, and set Refresh Token
     *
     * @param string $scope Sope parameter
     * @param Akita_OAuth2_Model_Authinfo $authInfo AuthInfo
     * @return boolen
     */
    abstract public function setRefreshToken( $authInfo );


    /**
     * check nonce and response_type
     *
     * @param string $response_type response_type parameter
     * @param string $nonce nonce parameter
     * @return boolen
     */
    //abstract public function validateNonce($response_type, $nonce);

    /**
     * check display param
     *
     * @param string $display display parameter
     * @return boolen
     */
    abstract public function validateDisplay($display);

    /**
     * check prompt param
     *
     * @param array $prompt prompt parameter
     * @return boolen
     */
    abstract public function validatePrompt($prompt);

    /**
     * check OpenID Request Object
     *
     * @param Akita_OpenID_Server_Request $request request object
     * @return boolen
     */
    abstract public function validateRequestObject($request);

    /**
     * check ID Token
     *
     * @param array $prompt prompt parameter
     * @param string $id_token ID Token parameter
     * @return boolen
     */
    abstract public function validateIDToken($prompt, $id_token);

    /**
     * create ID TOken for Authorization Response
     *
     * @return Akita_OpenIDConnect_Model_IDToken
     */
    abstract public function createIdToken();
        /**
         * payload:
         *  - iss : From OP Configuration
         *  - user_id : user_id and RP Configuration(public or pairwise)
         *  - aud : client_id
         *  - exp : OP's policy
         *  - iat : Current timestamp
         *  - acr : (optional)
         *  - nonce : (optilnal)
         *  - auth_time : (optional) 
         */
}
