<?php
/**
 * Akita_OpenIDConnect_Model_IDToken
 *
 * model class that represents ID token
 *
 * @category  OpenIDConnect
 * @package   Akita_OpenIDConnect
 * @author    Ryo Ito <ritou.06@gmail.com>
 * @copyright 2012 Ryo Ito
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @link      http://openpear.org/package/Akita_OpenIDConnect
 */
require_once dirname(__FILE__) . '/../Util/JOSE/JWS.php';

class Akita_OpenIDConnect_Model_IDToken
{
    private $_header = array();
    private $_payload = array();
    private $_tokenString = '';
    private $_key = null;

    /**
     * constructor
     *
     * @param array $header JWT Header
     * @param array $payload JWT Payload
     * @return ID Token instance
     */
    public function __construct($header=array(), $payload=array(), $key=null)
    {
        $this->setHeader($header);
        $this->setPayload($payload);
        $this->setKey($key);
    }

    // setter
    public function setHeaderItem($name, $value)
    {
        $this->_header[$name] = $value;
    }

    public function setPayloadItem($name, $value)
    {
        $this->_payload[$name] = $value;
    }

    public function setHeader($header)
    {
        $this->_header = $header;
    }

    public function setPayload($payload)
    {
        $this->_payload = $payload;
    }

    public function setTokenString($idTokenString)
    {
        $this->_tokenString = $idTokenString;
    }

    public function setKey($key)
    {
        $this->_key = $key;
    }

    public function setAccessTokenHash($accessTokenString)
    {
        // bit : 256/384/512 
        if(isset($this->_header['alg']) && $this->_header['alg'] != 'none'){
            $bit = substr($this->_header['alg'], 2, 3);
        }else{
            // TODO: Error case. throw exception???
            $bit = '256';
        }
        $len = ((int)$bit)/16;
        $this->_payload['at_hash'] = Akita_OpenIDConnect_Util_Base64::urlEncode(substr(hash('sha'.$bit, $accessTokenString, true), 0, $len));
    }

    public function setCodeHash($authorizationCodeString)
    {
        // bit : 256/384/512 
        if(isset($this->_header['alg']) && $this->_header['alg'] != 'none'){
            $bit = substr($this->_header['alg'], 2, 3);
        }else{
            // TODO: Error case. throw exception???
            $bit = '256';
        }
        $len = ((int)$bit)/16;
        $this->_payload['c_hash'] = Akita_OpenIDConnect_Util_Base64::urlEncode(substr(hash('sha'.$bit, $authorizationCodeString, true), 0, $len));
    }

    // getter
    public function getHeader()
    {
        return $this->_header;
    }

    public function getPayload()
    {
        return $this->_payload;
    }

    /**
     * generate ID Token string
     *
     * @param mixed $key private key or shared key
     * @return ID Token string
     */
    public function getTokenString()
    {
        // Support only JWS
        if(isset($this->_header['alg']) && Akita_OpenIDConnect_Util_JOSE_JWS::isAllowedAlg($this->_header['alg'])){
            // create JWS
            $jws = new Akita_OpenIDConnect_Util_JOSE_JWS($this->_header['alg']);
            foreach($this->_header as $name => $value ){
                if($name !== 'alg'){
                    $jws->setHeaderItem($name, $value);
                }
            }
            $jws->setPayload($this->_payload);
            $signatureBaseString = $jws->getSignatureBaseString(true);
            $jws->sign($signatureBaseString, $this->_key);
            return $jws->getTokenString(true);
        }else{
            throw new Exception('InvalidFormat');
        }
    }

    /**
     * generate ID Token string
     *
     * @param mixed $key private key or shared key
     * @return ID Token string
     */
    public function validate()
    {
        // generate Token String
        $idTokenString = $this->getTokenString($this->_key);
        return ($idTokenString == $this->_tokenString);
    }

    /**
     * load ID Token String and return object
     *
     * @param string $idTokenString ID Token String
     * @return Akita_OpenIDConnect_Model_IDToken 
     */
    static public function loadTokenString($idTokenString)
    {
        $header = Akita_OpenIDConnect_Util_JOSE_JWT::getHeader($idTokenString);
        $payload = Akita_OpenIDConnect_Util_JOSE_JWT::getPayload($idTokenString, true);

        // validation
        if(is_array($header) && is_array($payload)){
            $idTokenObj = new Akita_OpenIDConnect_Model_IDToken($header, $payload);
            $idTokenObj->setTokenString($idTokenString);
            return $idTokenObj;
        }else{
            throw new Exception('InvalidTokenFormat');
        }
    }
}
