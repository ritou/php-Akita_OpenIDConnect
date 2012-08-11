<?php

require_once 'Akita/OAuth2.php';
require_once dirname(__FILE__) . '/OpenIDConnect/Model/AuthInfo.php';
require_once dirname(__FILE__) . '/OpenIDConnect/Model/IDToken.php';
require_once dirname(__FILE__) . '/OpenIDConnect/Server/GrantHandlers.php';
require_once dirname(__FILE__) . '/OpenIDConnect/Server/Request.php';
require_once dirname(__FILE__) . '/OpenIDConnect/Server/AuthorizationHandler.php';
require_once dirname(__FILE__) . '/OpenIDConnect/Server/DataHandler.php';
require_once dirname(__FILE__) . '/OpenIDConnect/Server/UserInfo.php';
require_once dirname(__FILE__) . '/OpenIDConnect/Util/HttpClient.php';
require_once dirname(__FILE__) . '/OpenIDConnect/Util/JOSE/JWS.php';
