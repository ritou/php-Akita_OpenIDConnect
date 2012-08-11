<?php
/**
 * Akita_OpenIDConnect_Model_AuthInfo
 *
 * model class that represents authorization information
 *
 * @category  OpenIDConnect
 * @package   Akita_OpenIDConnect
 * @author    Ryo Ito <ritou.06@gmail.com>
 * @copyright 2012 Ryo Ito
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @link      http://openpear.org/package/Akita_OpenIDConnect
 */
require_once 'Akita/OAuth2/Model/AuthInfo.php';
class Akita_OpenIDConnect_Model_AuthInfo
    extends Akita_OAuth2_Model_AuthInfo
{
    public $idToken='';
    public $userInfoClaims=array();

    public function __construct(    $authId='', 
                                    $userId='', 
                                    $clientId='', 
                                    $scope='', 
                                    $refreshToken='', 
                                    $code='', 
                                    $redirectUri='',
                                    $idToken='',
                                    $userInfoClaims=array()
    ){
        parent::__construct(    $authId, 
                                $userId, 
                                $clientId, 
                                $scope, 
                                $refreshToken, 
                                $code, 
                                $redirectUri);
        $this->idToken = $idToken;
        $this->userInfoClaims = $userInfoClaims;
    }

}
