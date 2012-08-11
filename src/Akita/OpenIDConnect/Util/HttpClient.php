<?php
/**
 * Akita_OpenIDConnect_Util_HttpClient
 *
 * utility class for HTTP Client
 *
 * @category  OpenIDConnect
 * @package   Akita_OpenIDConnect
 * @author    Ryo Ito <ritou.06@gmail.com>
 * @copyright 2012 Ryo Ito
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @link      http://openpear.org/package/Akita_OpenIDConnect
 */
class Akita_OpenIDConnect_Util_HttpClient
{
    // CURL Info
    private $_useragent = 'OpenID Connect HTTP Client v0.0.1';
    private $_timeout = 3;
    private $_connecttimeout = 3;
    private $_ssl_verifypeer = TRUE;
    private $_ssl_verifyhost = TRUE;
    private $_responseheader = FALSE;
    // CURL response
    private $_http_info = array();
    private $_http_body = null;
    private $_http_code = null;

    // Constructor
    public function __construct()
    {
        // nothing to do
    }

    // HTTP GET
    public function get($url, $header=array())
    {
        $this->_http_info = array();
        $this->_http_code = null;
        $this->_http_body = null;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if(!empty($header)){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        curl_setopt($ch, CURLOPT_USERAGENT, $this->_useragent);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->_connecttimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->_timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_ssl_verifypeer);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->_ssl_verifyhost);
        curl_setopt($ch, CURLOPT_HEADER, $this->_responseheader);
        curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);

        $this->_http_body = curl_exec($ch);
        $this->_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->_http_info = array_merge($this->_http_info, curl_getinfo($ch));
        curl_close($ch);
        return $this->_http_body;
    }

    public function setTimeout($timeout, $connectTimeout)
    {
        $this->_timeout = $timeout;
        $this->_connecttimeout = $connectTimeout;
    }

    public function setSslVerify($ssl_verifypeer, $ssl_verifyhost)
    {
        $this->_ssl_verifypeer = $ssl_verifypeer;
        $this->_ssl_verifyhost = $ssl_verifyhost;
    }

    // get Last Http body
/*
    public function getLastResponseBody()
    {
        return $this->_http_body;
    }
*/

    // get Last Http response
/*
    public function getLastResponseCode()
    {
        return $this->_http_code;
    }
*/

    // get Last Http info
/*
    public function getLastResponseInfo()
    {
        return $this->_http_info;
    }
*/
}
