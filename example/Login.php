<?php

session_name('AkitaOpenIDConnectServerSample');
session_start();

$token_from_session = $_SESSION['token'];
$_SESSION['token'] = '';
if( !empty($token_from_session) && $token_from_session == $_POST['token'] ){
    if( $_POST['email'] == 'fakeuser@example.com' &&
        $_POST['password'] == 'fakepassword'
        ){
        // log in
        $_SESSION['email'] = $_POST['email'];
        $redirect_uri = ( $_SESSION['redirect_uri'] ) ? $_SESSION['redirect_uri'] : './Authorization.php';
        $_SESSION['redirect_uri'] = '';
        header('Location: '.$redirect_uri);
        exit;
    }
}

// display form page
$token = hash('sha256', 'token'.time().mt_rand());
$_SESSION['token'] = $token;

include('./tmpl/login.html');
