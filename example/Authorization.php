<?php

session_name('AkitaOpenIDConnectServerSample');
session_start();

require_once './lib/DataHandler.php';

// process request
$headers = apache_request_headers();
$request = new Akita_OpenIDConnect_Server_Request('authorization', $_SERVER, $_GET, $headers);
$dataHandler = new Akita_OpenIDConnect_Server_Sample_DataHandler($request);
$authHandler = new Akita_OpenIDConnect_Server_AuthorizationHandler();
try{
    $authHandler->processAuthorizationRequest($dataHandler);
}catch(Akita_OAuth2_Server_Error $error){
    // error handling
    include('./tmpl/error.html');
    exit;
}

// login
$email = $_SESSION['email'];
$redirectUri = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
$_SESSION['redirect_uri'] = $redirectUri;
if(empty($email)){
    header('location: ./Login.php');
    exit;
}

// store request
$_SESSION['server_request'] = serialize($request);
$_SESSION['server_ts'] = time();
$_SESSION['server_key'] = mt_rand();
$request_hash = hash_hmac('sha256', $_SESSION['server_request'].$_SESSION['server_ts'], $_SESSION['server_key']);
$denied_url = str_replace('Authorization.php','Finish.php','http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']).'?request_hash='.urlencode($request_hash).'&deny=1';

include('./tmpl/authorization.html');
