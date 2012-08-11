<?php
/**
 * Akita_OpenIDConnect_Server_Request
 *
 * Simple HTTP Request class for OpenID Connect Endpoint.
 *
 * PHP versions 5
 *
 * LICENSE: MIT License
 *
 * @category  OAuth2
 * @package   Akita_OpenIDConnect
 * @author    Ryo Ito <ritou.06@gmail.com>
 * @copyright 2012 Ryo Ito
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @link      http://openpear.org/package/Akita_OpenIDConnect
 */

/**
 * Akita_OpenIDConnect_Server_Request
 *
 * Simple HTTP Request class for OpenID Connect Endpoint.
 *
 * @category  OpenIDConnect
 * @package   Akita_OpenIDConnect
 * @author    Ryo Ito <ritou.06@gmail.com>
 * @copyright 2012 Ryo Ito
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @link      http://openpear.org/package/Akita_OpenIDConnect
 */
require_once 'Akita/OAuth2/Server/Request.php';
require_once dirname(__FILE__) . '/../Util/HttpClient.php';

class Akita_OpenIDConnect_Server_Request
    extends Akita_OAuth2_Server_Request
{
    // OpenID Request Object
    public $openidConnectRequest = "";
    public $openidConnectResponseType = "";
    public $openidConnectScope = array();
    public $openidConnectPrompt = array();

    // constructor
    public function __construct($endpoint_type,
                                $server, 
                                $params=array(),
                                $headers=array(),
                                $httpClient=null)
    {
        parent::__construct($endpoint_type, $server, $params, $headers);
        $this->setRequestObject($params, $httpClient);
        $this->setScope($params);
        $this->setResponseType($params);
        $this->setPrompt($params);
    }

    // set OpenID Request Object
    private function setRequestObject($params, $httpClient=null)
    {
        if(is_null($httpClient)){
            $httpClient = new Akita_OpenIDConnect_Util_HttpClient();
        }
        // get request string
        if(isset($params['request_uri'])){
            // from request_file param
            $this->openidConnectRequest = rtrim($httpClient->get($params['request_uri']));
        }elseif(isset($params['request'])){
            // from request param
            $this->openidConnectRequest = $params['request'];
        }
    }

    private function setScope($params)
    {
        if(isset($params['scope'])){
            if(strpos($params['scope'], ' ') !== false){
                $this->openidConnectScope = explode(' ', $params['scope']);
            }else{
                $this->openidConnectScope = array($params['scope']);
            }
        }
    }

    private function setResponseType($params)
    {
        if(isset($params['response_type'])){
            if(strpos($params['response_type'], ' ') !== false){
                // split 
                $types = explode(' ', $params['response_type']);
                // sort
                sort($types, SORT_STRING);
                $this->openidConnectResponseType = implode(' ', $types);
            }else{
                $this->openidConnectResponseType = $params['response_type'];
            }
        }        
    }

    private function setPrompt($params)
    {
        if(isset($params['prompt'])){
            if(strpos($params['prompt'], ' ') !== false){
                $this->openidConnectPrompt = explode(' ', $params['prompt']);
            }else{
                $this->openidConnectPrompt = array($params['prompt']);
            }
        }
    }
}
