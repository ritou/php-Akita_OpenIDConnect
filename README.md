# php-Akita_OpenIDConnect #

This is PHP library for OpenID Connect Server(OP).  

## Specifications to support ##

- [http://openid.net/specs/openid-connect-basic-1_0.html](http://openid.net/specs/openid-connect-basic-1_0.html "OpenID Connect Basic Client Profile 1.0")
- [http://openid.net/specs/openid-connect-implicit-1_0.html](http://openid.net/specs/openid-connect-implicit-1_0.html "OpenID Connect Implicit Client Profile 1.0")
- [http://openid.net/specs/openid-connect-standard-1_0.html](http://openid.net/specs/openid-connect-standard-1_0.html "OpenID Connect Standard 1.0")
- [http://openid.net/specs/openid-connect-messages-1_0.html](http://openid.net/specs/openid-connect-messages-1_0.html "OpenID Connect Messages 1.0")
- [http://openid.net/specs/oauth-v2-multiple-response-types-1_0.html](http://openid.net/specs/oauth-v2-multiple-response-types-1_0.html "OAuth 2.0 Multiple Response Type Encoding Practices")

## Source ##

    src/
    `-- Akita
        |-- OpenIDConnect
        |   |-- Model
        |   |   |-- AuthInfo.php
        |   |   `-- IDToken.php
        |   |-- Server
        |   |   |-- AuthorizationHandler.php
        |   |   |-- DataHandler.php
        |   |   |-- GrantHandler
        |   |   |   `-- AuthorizationCode.php
        |   |   |-- GrantHandlers.php
        |   |   |-- Request.php
        |   |   `-- UserInfo.php
        |   `-- Util
        |       |-- Base64.php
        |       |-- HttpClient.php
        |       |-- JOSE
        |       |   |-- JWS.php
        |       |   `-- JWT.php
        |       `-- Json.php
        `-- OpenIDConnect.php

### DataHandler ###

*Akita_OpenIDConnect_Server_DataHandler* class is abstract class.
You must inherit it and implement own processing.

    class Your_DataHandler
        extends Akita_OpenIDConnect_Server_DataHandler
    {
        ....
    }

### Endpoints ###

There are classes implementing each endpoint.  
The processing defines it in the DataHandler class mentioned above.

#### Authorization Endpoint ####

The *Akita_OpenIDConnect_Server_AuthorizationHandler* class implements the handling of request and the processing after the agreement.  

    // process request
    $headers = apache_request_headers();
    $server = $_SERVER;
    $params = $_GET;
    
    $request = new Akita_OpenIDConnect_Server_Request('authorization', $server, $params, $headers);
    $dataHandler = new Your_DataHandler($request);
    $authHandler = new Akita_OpenIDConnect_Server_AuthorizationHandler();
    try{
        $authHandler->processAuthorizationRequest($dataHandler);
    }catch(Akita_OAuth2_Server_Error $error){
        .... // error handling
    }
    
    // after the agreement
    try{
        if(.... // User denied){
            $respose = $authHandler->denyAuthorizationRequest($dataHandler);
        }else{ // User allowed
            $respose = $authHandler->allowAuthorizationRequest($dataHandler);
        }
    }catch(Akita_OAuth2_Server_Error $error){
        .... // error handling
    }

#### Token Endpoint ####

The *Akita_OpenIDConnect_Server_GrantHandlers* class returns handler for each grant type.  
Grant handler process the request and returns response data.  

    // process request
    $headers = apache_request_headers();
    $server = $_SERVER;
    $params = $_POST;
    
    $request = new Akita_OpenIDConnect_Server_Request('authorization', $server, $params, $headers);
    $dataHandler = new Your_DataHandler($request);
    try{
        $grantHandler = Akita_OpenIDConnect_Server_GrantHandlers::getHandler($request->param['grant_type']);
        $res = $grantHandler->handleRequest($dataHandler);
        }catch(Akita_OAuth2_Server_Error $error){
        .... // error handling
    }

There are four grant handlers.

- *authorization_code* : *Akita_OpenIDConnect_Server_GrantHandler_AuthorizationCode*
- *refresh_token* : *Akita_OAuth2_Server_GrantHandler_RefreshToken*
- *client_credentials* : *Akita_OAuth2_Server_GrantHandler_ClientCredentials*
- *password* : *Akita_OAuth2_Server_GrantHandler_Password*

### UserInfo Endpoint ###

*Akita_OpenIDConnect_Server_UserInfo* class returns AuthInfo class, and it contains user-claims which is requested by client.

    $headers = apache_request_headers();
    $server = $_SERVER;
    $params = $_GET;
    
    $request = new Akita_OpenIDConnect_Server_Request('authorization', $server, $params, $headers);
    $dataHandler = new Your_DataHandler($request);
    $resource = new Akita_OpenIDConnect_Server_UserInfo();
    try{
        $authInfo = $resource->processRequest($dataHandler);
        }catch(Akita_OAuth2_Server_Error $error){
        .... // error handling
    }

### Model ###

- *Akita_OpenIDConnect_Model_AuthInfo* : represents authorization information
- *Akita_OpenIDConnect_Model_IDToken* : represents ID Token

### Utility ###

- *Akita_OpenIDConnect_Util_Base64* : Base 64 URL Encode/Decode
- *Akita_OpenIDConnect_Util_HttpClient* : Simple HTTP client to fetch OpenID request object
- *Akita_OpenIDConnect_Util_Json* : JSON encode for php-5.2
- *Akita_OpenIDConnect_Util_JOSE_** : JWT/JWS

## Example ##

[http://www8322u.sakura.ne.jp/php-Akita_OpenIDConnect/example/Client.php](http://www8322u.sakura.ne.jp/php-Akita_OpenIDConnect/example/Client.php "Akita_OpenIDConnect Sample OP/RP")

AUTHOR
------------------------------------------------------
@ritou ritou@gmail.com

LISENCE
------------------------------------------------------
MIT Lisense.
