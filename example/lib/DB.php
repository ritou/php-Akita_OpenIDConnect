<?php

class Akita_OpenIDConnect_Server_Sample_DB
{
    private $_authInfo;
    private $_accessToken;

    public function __construct(){
        $mongo = new Mongo();
        $db = $mongo->Akita_OAuth2_Server_Sample;

        $this->_authInfo = $db->AuthInfo;
        $this->_accessToken = $db->AccessToken;
    }

    public function setAuthInfo($authInfo, $exp, $use_flg=0){
        $data = array();
        $data['authId'] = $authInfo->authId;
        $data['code'] = $authInfo->code;
        $data['clienId'] = $authInfo->clientId;
        $data['userId'] = $authInfo->userId;
        $data['refreshToken'] = $authInfo->refreshToken;
        $data['exp'] = $exp;
        $data['use_flg'] = $use_flg;
        $data['body'] = serialize($authInfo);

        $current_data = $this->getAuthInfoByAuthId($data['authId']);
        if(is_null($current_data)){
            $this->_authInfo->insert($data);
        }else{
            $this->_authInfo->update(array('authId'=>$data['authId']), $data);
        }
    }

    public function getAuthInfo($clientId, $userId, $scope){
        $serialized = $this->_authInfo->findOne(
                                                    array(
                                                        'clientId'=>$clientId,
                                                        'userId'=>$userId,
                                                        'scope'=>$scope
                                                        )
                                                );
        if(!is_null($serialized)){
            return unserialize($serialized['body']);
        }else{
            return null;
        }
    }

    public function getAuthInfoByAuthId($authId){
        $serialized = $this->_authInfo->findOne(array('authId'=>$authId));
        $current_ts = time();
        if(!is_null($serialized) && $serialized['exp'] >= $current_ts ){
            return unserialize($serialized['body']);
        }else{
            return null;
        }
    }

    public function getAuthInfoByCode($code){
        $serialized = $this->_authInfo->findOne(array('code'=>$code,'use_flg'=>0));
        $current_ts = time();
        if(!is_null($serialized) && $serialized['exp'] >= $current_ts ){
            return unserialize($serialized['body']);
        }else{
            return null;
        }
    }

    public function getAuthInfoByRefreshToken($refreshToken){
        $serialized = $this->_authInfo->findOne(array('refreshToken'=>$refreshToken));
        $current_ts = time();
        if(!is_null($serialized) && $serialized['exp'] >= $current_ts ){
            return unserialize($serialized['body']);
        }else{
            return null;
        }
    }

    public function setAccessToken($accessToken){
        $data = array();
        $data['token'] = $accessToken->token;
        $data['body'] = serialize($accessToken);

        $current_data = $this->getAccessTokenByToken($data['token']);
        if(is_null($current_data)){
            $this->_accessToken->insert($data);
        }else{
            $this->_accessToken->update(array('token'=>$data['token']), $data);
        }
    }

    public function getAccessTokenByToken($token){
        $serialized = $this->_accessToken->findOne(array('token'=>$token));
        if(!is_null($serialized)){
            return unserialize($serialized['body']);
        }else{
            return null;
        }
    }
}
