<?php

// This is OpenIDConnect Sample Protected Resource

// process Request
require_once './lib/DataHandler.php';

// process request
$headers = apache_request_headers();
$request = new Akita_OpenIDConnect_Server_Request('resource', $_SERVER, $_GET, $headers);
$dataHandler = new Akita_OpenIDConnect_Server_Sample_DataHandler($request);
$resource = new Akita_OpenIDConnect_Server_UserInfo();
try{
    $authInfo = $resource->processRequest($dataHandler);
}catch(Akita_OAuth2_Server_Error $error){
    // error handling
    header('HTTP/1.1 '.$error->getOAuth2Code());
    header('Content-Type: application/json;charset=UTF-8');
    header('Cache-Control: no-store');
    header('Pragma: no-cache');
    $res = array();
    $res['error'] = $error->getOAuth2Error();
    $desc = $error->getOAuth2ErrorDescription();
    if(!empty($desc)){
        $res['error_description'] = $desc;
    }
    echo Akita_OpenIDConnect_Util_Json::encode($res);
    exit;
}

// build response
$res = array();
foreach($authInfo->userInfoClaims as $claim_name){
    $res[$claim_name] = $claim_name."_value";
}

header('HTTP/1.1 200 OK');
header('Content-Type: application/json;charset=UTF-8');
header('Cache-Control: no-store');
header('Pragma: no-cache');
echo Akita_OpenIDConnect_Util_Json::encode($res);
