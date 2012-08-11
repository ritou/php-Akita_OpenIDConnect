<?php

/**
 * Akita_OpenIDConnect_Server_GrantHandler_AuthorizationCode
 *
 * GrantHandler class for AuthorizationCode grant type
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
require_once 'Akita/OAuth2/Server/GrantHandler/AuthorizationCode.php';

/**
 * Akita_OpenIDConnect_Server_GrantHandler_AuthrizationCode
 *
 * GrantHandler class for AuthorizationCode grant type
 *
 * @category  OpenIDConnect
 * @package   Akita_OpenIDConnect
 * @author    Ryo Ito <ritou.06@gmail.com>
 * @copyright 2012 Ryo Ito
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @link      http://openpear.org/package/Akita_OpenIDConnect
 */
class Akita_OpenIDConnect_Server_GrantHandler_AuthorizationCode
    extends Akita_OAuth2_Server_GrantHandler_AuthorizationCode
{
    public function handleRequest($dataHandler)
    {
        $request = $dataHandler->getRequest();

        $code = (isset($request->param['code'])) ? $request->param['code'] : "";
        if(empty($code)){
            throw new Akita_OAuth2_Server_Error(
                '400',
                'invalid_request',
                "'code' is required"
            );
        }

        $redirect_uri = (isset($request->param['redirect_uri'])) ? $request->param['redirect_uri'] : "";
        if(empty($redirect_uri)){
            throw new Akita_OAuth2_Server_Error(
                '400',
                'invalid_request',
                "'redirect_uri' is required"
            );
        }

        // validate client credential
        $client_id = (isset($request->param['client_id'])) ? $request->param['client_id'] : "";
        $client_secret = (isset($request->param['client_secret'])) ? $request->param['client_secret'] : "";
        if(!$dataHandler->validateClient($client_id, $client_secret, 'authorization_code')){
            throw new Akita_OAuth2_Server_Error(
                '401',
                'invalid_client'
            );
        }

        // obtain AuthInfo from AuthorizationCode
        $authInfo = $dataHandler->getAuthInfoByCode($code);
        if(is_null($authInfo)){
            throw new Akita_OAuth2_Server_Error(
                '400',
                'invalid_grant',
                "'code' is invalid"
            );
        }

        if($authInfo->clientId != $client_id){
            throw new Akita_OAuth2_Server_Error(
                '400',
                'invalid_grant',
                "'client_id' is not matched"
            );
        }

        if($authInfo->redirectUri != $redirect_uri){
            throw new Akita_OAuth2_Server_Error(
                '400',
                'invalid_grant',
                "'redirect_uri' is not matched"
            );
        }

        // remove authorization code from authInfo
        $authInfo = $dataHandler->setRefreshToken($authInfo);

        // obtain AccessToken from AuthInfo
        $accessToken = $dataHandler->createOrUpdateAccessToken(
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

        // build response
        $res = $accessToken->getResponse();
        $res['refresh_token'] = $authInfo->refreshToken;
        $res['id_token'] = $authInfo->idToken;

        return $res;
    }
}
