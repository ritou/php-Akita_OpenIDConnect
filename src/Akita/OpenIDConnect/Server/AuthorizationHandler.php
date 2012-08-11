<?php
/**
 * Akita_OpenIDConnect_Server_AuthorizationHandler
 *
 * AuthorizationHandler class
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
 * @link      http://openpear.org/package/Akita_OpenIDConnect
 */

/**
 * Akita_OpenIDConnect_Server_AuthorizationHandler
 *
 * AuthorizationHandler class
 *
 * @category  OpenIDConnect
 * @package   Akita_OpenIDConnect
 * @author    Ryo Ito <ritou.06@gmail.com>
 * @copyright 2012 Ryo Ito
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @link      http://openpear.org/package/Akita_OpenIDConnect
 */
require_once 'Akita/OAuth2/Server/AuthorizationHandler.php';
class Akita_OpenIDConnect_Server_AuthorizationHandler
    extends Akita_OAuth2_Server_AuthorizationHandler
{
    /**
     * process Authorization Request
     *
     * @param Akita_OpenIDConnect_Server_DataHandler $dataHandler
     */
    public function processAuthorizationRequest($dataHandler, $allowed_response_type=array( 'code', 'id_token', 'token',
                                                                                            'code id_token', 'code token', 'id_token token',
                                                                                            'code id_token token'))
    {
        $request = $dataHandler->getRequest();

        //$response_type = (isset($request->param['response_type'])) ? $request->param['response_type'] : "";
        $response_type = $request->openidConnectResponseType;
        if (empty($response_type)) {
            throw new Akita_OAuth2_Server_Error(
                '400',
                'invalid_request',
                "'response_type' is required"
            );
        }
        if(!in_array($response_type, $allowed_response_type)){
            throw new Akita_OAuth2_Server_Error(
                '400',
                'unsupported_response_type'
            );
        }
        
        // validate client_id
        $client_id = (isset($request->param['client_id'])) ? $request->param['client_id'] : "" ;
        if (empty($client_id)) {
            throw new Akita_OAuth2_Server_Error(
                '400',
                'invalid_request',
                "'client_id' is required"
            );
        }
        if (!$dataHandler->validateClientById( $client_id )) {
            throw new Akita_OAuth2_Server_Error(
                '400',
                'unauthorized_client'
            );
        }

        // validate redirect_uri
        $redirect_uri = (isset($request->param['redirect_uri'])) ? $request->param['redirect_uri'] : "";
        if(empty($redirect_uri)){
            throw new Akita_OAuth2_Server_Error(
                '400',
                'invalid_request',
                "'redirect_uri' is required"
            );
        }
        if(!$dataHandler->validateRedirectUri($client_id, $redirect_uri)){
            throw new Akita_OAuth2_Server_Error(
                '400',
                'invalid_request',
                "'redirect_uri' is invalid"
            );
        }

        // validate scope
        $scope = $request->openidConnectScope;
        if(!$dataHandler->validateScope($client_id, $scope)){
            throw new Akita_OAuth2_Server_Error(
                '400',
                'invalid_scope'
            );
        }

        // validate nonce
        $nonce = (isset($request->param['nonce'])) ? $request->param['nonce'] : "";
        //if(!$dataHandler->validateNonce($response_type, $nonce)){
        if( (   $response_type != 'code' &&
                $response_type != 'token')
            && empty($nonce)){
            throw new Akita_OAuth2_Server_Error(
                '400',
                'invalid_request',
                'nonce_required'
            );
        }
        
        // validate display
        $display = (isset($request->param['display'])) ? $request->param['display'] : "";
        if(!$dataHandler->validateDisplay($display)){
            throw new Akita_OAuth2_Server_Error(
                '400',
                'invalid_request',
                "'display' is invalid"
            );
        }

        // validate prompt
        $prompt = $request->openidConnectPrompt;
        if(!$dataHandler->validatePrompt($prompt)){
            throw new Akita_OAuth2_Server_Error(
                '400',
                'invalid_request',
                "'prompt' is invalid"
            );
        }

        // validate request
        if(isset($request->param['request'])){
            if(!$dataHandler->validateRequestObject($request)){
                throw new Akita_OAuth2_Server_Error(
                    '400',
                    'invalid_request',
                    "'request' is invalid"
                );
            }
        }

        // validate request_uri
        if(isset($request->param['request_uri'])){
            if(!$dataHandler->validateRequestObject($request)){
                throw new Akita_OAuth2_Server_Error(
                    '400',
                    'invalid_request',
                    "'request_uri' is invalid"
                );
            }
        }

        // validate id_token
        $id_token = (isset($request->param['id_token'])) ? $request->param['id_token'] : "";
        if(!$dataHandler->validateIDToken($prompt, $id_token)){
            throw new Akita_OAuth2_Server_Error(
                '400',
                'interaction_required' // TODO: Error Code my be wrong.
            );
        }
    }

    /**
     * create AuthInfo and AccessToken and build response
     *
     * @param Akita_OpenIDConnect_Server_DataHandler $dataHandler
     */
    public function allowAuthorizationRequest($dataHandler)
    {
        $this->processAuthorizationRequest( $dataHandler );
        $request = $dataHandler->getRequest();
        
        $client_id = $request->param['client_id'];
        $user_id = $dataHandler->getUserId();
        $scope = $request->param['scope'];

        // create ID Token 
        $idToken =  $dataHandler->createIdToken();
        if(is_null($idToken)){
            throw new Akita_OAuth2_Server_Error(
                '500',
                'server_error'
            );
        }

        // create AuthInfo
        $authInfo = $dataHandler->createOrUpdateAuthInfo(
            array(
                'clientId' => $client_id,
                'userId'   => $user_id,
                'scope'     => $scope,
                'grant_type'     => 'authorization_code',
                'idToken' => $idToken
            )
        );
        if(is_null($authInfo)){
            throw new Akita_OAuth2_Server_Error(
                '500',
                'server_error'
            );
        }

        // create Access Token 
        $accessToken = null;
        if(     $request->param['response_type'] == "token" || 
                $request->param['response_type'] == "code token" ||
                $request->param['response_type'] == "id_token token" ||
                $request->param['response_type'] == "code id_token token"
            ){
            $accessToken =  $dataHandler->createOrUpdateAccessToken(
                array(
                    'authInfo'  => $authInfo
                )
            );
            if(is_null($accessToken)){
                throw new Akita_OAuth2_Server_Error(
                    '500',
                    'server_error'
                );
            }
        }

        // build response
        $params = array();

        // set Access Token
        if(!is_null($accessToken)){
            $params = $accessToken->getResponse();
        }

        // set Authorization Code
        if(     $request->param['response_type'] == "code" || 
                $request->param['response_type'] == "code id_token" ||
                $request->param['response_type'] == "code token" ||
                $request->param['response_type'] == "code id_token token"
                ){
            $params['code'] = $authInfo->code;
        }

        // set id_token
        if(     $request->param['response_type'] == "id_token" || 
                $request->param['response_type'] == "code id_token" ||
                $request->param['response_type'] == "id_token token" ||
                $request->param['response_type'] == "code id_token token"
                ){
            if(!empty($params['access_token'])){
                $idToken->setAccessTokenHash($params['access_token']);
            }
            if(!empty($params['code'])){
                $idToken->setCodeHash($params['code']);
            }
            $params['id_token'] = $idToken->getTokenString();
        }

        // state
        $state = (isset($request->param['state'])) ? $request->param['state'] : "";
        if(!empty($state)){
            $params['state'] = $state;
        }

        // build response
        if(empty($params['access_token']) && empty($params['id_token'])){
            $res = array(
                'redirect_uri' => $request->param['redirect_uri'],
                'query'     => $params,
                'fragment'  => array()
            );
        }else{
            $res = array(
                'redirect_uri' => $request->param['redirect_uri'],
                'query'     => array(),
                'fragment'  => $params
            );
        }
        return $res;
    }

    /**
     * build denied response
     *
     * @param Akita_OpenIDConnect_Server_DataHandler $dataHandler
     */
//    public function denyAuthorizationRequest($dataHandler)
//        parent::
//    }
}
