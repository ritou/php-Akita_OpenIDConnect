<?php

session_name('AkitaOpenIDConnectServerSample');
session_start();

require_once './lib/DataHandler.php';

// login check
$email = $_SESSION['email'];
$redirectUri = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
$_SESSION['redirect_uri'] = $redirectUri;
if(empty($email)){
    header('location: ./Login.php');
    exit;
}

$request = null;
$request_hash = $_GET['request_hash'];
if( isset($_SESSION['server_request']) && !empty($_SESSION['server_request']) &&
    isset($_SESSION['server_ts']) && !empty($_SESSION['server_ts']) &&
    isset($_SESSION['server_key']) && !empty($_SESSION['server_key']) &&
    ((time() - 300) < $_SESSION['server_ts']) &&
    ($_SESSION['server_ts'] <= time()) &&
    ($request_hash == hash_hmac('sha256', $_SESSION['server_request'].$_SESSION['server_ts'], $_SESSION['server_key']))
){
    $request = unserialize($_SESSION['server_request']);
    unset($_SESSION['server_request']);
    unset($_SESSION['server_ts']);
    unset($_SESSION['server_key']);
}else{
    // error handling
    $error = new Akita_OAuth2_Server_Error('400', 'invalid_request');
    include('./tmpl/error.html');
    exit;
}

$dataHandler = new Akita_OpenIDConnect_Server_Sample_DataHandler($request);
$dataHandler->setUserId($email);
$authHandler = new Akita_OpenIDConnect_Server_AuthorizationHandler();
try{
    if(isset($_GET['deny']) && $_GET['deny'] == '1'){
        $res = $authHandler->denyAuthorizationRequest($dataHandler);
    }else{
        $res = $authHandler->allowAuthorizationRequest($dataHandler);
    }
}catch(Akita_OAuth2_Server_Error $error){
    // error handling
    include('./tmpl/error.html');
    exit;
}

// build response
$redirect_uri = $res['redirect_uri'];
if(!empty($res['query'])){
    $redirect_uri .= (strpos($redirect_uri,'?')===false) ? '?' : '&';
    $redirect_uri .= http_build_query($res['query']);
}

if(!empty($res['fragment'])){
    $redirect_uri .= '#'.http_build_query($res['fragment']);
}
header('Location: '.$redirect_uri);
