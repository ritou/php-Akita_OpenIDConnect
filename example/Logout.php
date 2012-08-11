<?php

session_name('AkitaOpenIDConnectServerSample');
session_start();

$_SESSION['email'] = '';
$redirect_uri = ( $_SESSION['redirect_uri'] ) ? $_SESSION['redirect_uri'] : './Authorization.php';
header('Location: '.$redirect_uri);
