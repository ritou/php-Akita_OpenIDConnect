<?php

require_once './lib/DataHandler.php';

// process request
$headers = apache_request_headers();
$request = new Akita_OpenIDConnect_Server_Request('authorization', $_SERVER, $_POST, $headers);
$dataHandler = new Akita_OpenIDConnect_Server_Sample_DataHandler($request);
try{
    $grantHandler = Akita_OpenIDConnect_Server_GrantHandlers::getHandler($request->param['grant_type']);
    $res = $grantHandler->handleRequest($dataHandler);
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

header('HTTP/1.1 200 OK');
header('Content-Type: application/json;charset=UTF-8');
header('Cache-Control: no-store');
header('Pragma: no-cache');
echo Akita_OpenIDConnect_Util_Json::encode($res);
