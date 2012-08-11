<?php

require_once '../src/Akita/OpenIDConnect.php';

class OAuth2_Client_Code{

    // Client Credential
    private $_client_id;
    private $_client_secret;
    private $_redirect_uri;
    // CURL Info
    private $_useragent = 'OAuth 2.0 Sample Client v0.0.0';
    private $_timeout = 3;
    private $_connecttimeout = 3;
    private $_ssl_verifypeer = TRUE;
    private $_ssl_verifyhost = TRUE;
    private $_responseheader = FALSE;
    // CURL response
    private $_http_body = null;
    private $_http_code = null;
    private $_http_info = null;

    public function __construct($client_id, $client_secret, $redirect_uri) {
        $this->_client_id = $client_id;
        $this->_client_secret = $client_secret;
        $this->_redirect_uri = $redirect_uri;
    }

    public function getRequestAuthUrl($authz_endpoint, $scope="") {
        $url =  $authz_endpoint .
                "?client_id=" . urlencode($this->_client_id) .
                "&redirect_uri=" . urlencode($this->_redirect_uri) .
                "&response_type=code";
        If(!empty($scope)){
            $url .= "&scope=" . urlencode($scope);
        }
        return $url;
    }

    public function getAccessToken($token_endpoint, $code) {
        $params = array(
            "grant_type" => "authorization_code",
            "code" => $code,
            "redirect_uri" => $this->_redirect_uri,
            "client_id" => $this->_client_id,
            "client_secret" => $this->_client_secret
        );
        $this->_http_info = array();
        $this->_http_code = null;
        $this->_http_body = null;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $token_endpoint);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->_useragent);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->_connecttimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->_timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_ssl_verifypeer);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->_ssl_verifyhost);
        curl_setopt($ch, CURLOPT_HEADER, $this->_responseheader);
        curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_POST, TRUE);

        $this->_http_body = curl_exec($ch);
        $this->_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->_http_info = array_merge($this->_http_info, curl_getinfo($ch));
        curl_close($ch);
        return json_decode($this->_http_body);
    }

    public function refreshAccessToken($token_endpoint, $refresh_token) {
        $params = array(
            "grant_type" => "refresh_token",
            "refresh_token" => $refresh_token,
            "client_id" => $this->_client_id,
            "client_secret" => $this->_client_secret
        );
        $headers = array("Content-Type: application/x-www-form-urlencoded");
        $this->_http_info = array();
        $this->_http_code = null;
        $this->_http_body = null;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $token_endpoint);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->_useragent);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->_connecttimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->_timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_ssl_verifypeer);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->_ssl_verifyhost);
        curl_setopt($ch, CURLOPT_HEADER, $this->_responseheader);
        curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_POST, TRUE);

        $this->_http_body = curl_exec($ch);
        $this->_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->_http_info = array_merge($this->_http_info, curl_getinfo($ch));
        curl_close($ch);
        return json_decode($this->_http_body);
    }

    public function setToken($token) {
        $this->_token = $token;
        return true;
    }

    public function setTimeout($timeoutsec) {
        if (is_numeric($timestamp)) {
            $this->_timeout = $timeoutsec;
        }
    }

    public function setConnectTimeout($ctimeoutsec) {
        if (is_numeric($ctimestamp)) {
            $this->_connecttimeout = $ctimeoutsec;
        }
    }

    public function disableSSLChecks() {
        $this->_ssl_verifypeer = false;
        $this->_ssl_verifyhost = false;
    }

    public function enableSSLChecks() {
        $this->_ssl_verifypeer = true;
        $this->_ssl_verifyhost = true;
    }

    public function disableResponseHeader() {
        $this->_responseheader = false;
    }

    public function enableResponseHeader() {
        $this->_responseheader = true;
    }

    public function sendRequest($method, $url) {

        if(strpos($url, '?')===false){
            $url .= "?";
        }else{
            $url .= "&";
        }
        $access_url = $url . "access_token=" . urlencode($this->_token);

        $this->_http_info = array();
        $this->_http_code = null;
        $this->_http_body = null;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $access_url);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->_useragent);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->_connecttimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->_timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_ssl_verifypeer);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->_ssl_verifyhost);
        curl_setopt($ch, CURLOPT_HEADER, $this->_responseheader);
        curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);

        switch ($method) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, TRUE);
                if (!empty($entitybody)) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $entitybody);
                }
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                if (!empty($entitybody)) {
                    $url = "{$url}?{$entitybody}";
                }
        }

        $this->_http_body = curl_exec($ch);
        $this->_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->_http_info = array_merge($this->_http_info, curl_getinfo($ch));
        curl_close($ch);
        return ($this->_http_code = 200) ? true : false;
    }

    public function getLastResponse() {
        return $this->_http_body;
    }

    public function getLastResponseInfo() {
        return $this->_http_info;
    }

    public function getLastRequestHeader() {
        return $this->_http_info["request_header"];
    }
}

$client_id = "cid00001";
$client_secret = "csecret00001";
$redirect_uri = "http://".$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"];
$authZ_endpoint = str_replace("Client.php","Authorization.php",$redirect_uri);
$token_endpoint = str_replace("Client.php","Token.php",$redirect_uri);
$protected_resource = str_replace("Client.php","Resource.php",$redirect_uri)."?schema=openid";

$client = new OAuth2_Client_Code($client_id, $client_secret, $redirect_uri);

if(isset($_GET["code"]) && !empty($_GET["code"])){
    $code = $_GET["code"];

    $accessToken_1 = $client->getAccessToken($token_endpoint, $code);

    // ID Token verification
    $idToken_header = array();
    $idToken_payload = array();
    $idToken_is_valid = false;
    try{
        $idToken = Akita_OpenIDConnect_Model_IDToken::loadTokenString($accessToken_1->id_token);
        $idToken_header = $idToken->getHeader();
        $idToken_payload = $idToken->getPayload();
        $idToken->setKey("dummy_key");
        $idToken_is_valid = $idToken->validate();
    }catch(Exception $e){
        // id_token is invalid
    }

    $client->setToken($accessToken_1->access_token);
    $client->sendRequest("GET", $protected_resource);
    $resource_1 = $client->getLastResponse();
    $userinfo_1 = json_decode($resource_1, true);

    $accessToken_2 = $client->refreshAccessToken($token_endpoint, $accessToken_1->refresh_token);
    $client->setToken($accessToken_2->access_token);
    $client->sendRequest("GET", $protected_resource);
    $resource_2 = $client->getLastResponse();
    $userinfo_2 = json_decode($resource_2, true);
}
$authZ_url = $client->getRequestAuthUrl($authZ_endpoint, "openid profile");

include("./tmpl/client.html");
