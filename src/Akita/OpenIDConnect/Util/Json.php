<?php
/**
 * Akita_OpenIDConnect_Util_Json
 *
 * utility class for JSON Encode/Decode
 *
 * @category  OpenIDConnect
 * @package   Akita_OpenIDConnect
 * @author    Ryo Ito <ritou.06@gmail.com>
 * @copyright 2012 Ryo Ito
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @link      http://openpear.org/package/Akita_OpenIDConnect
 */
class Akita_OpenIDConnect_Util_Json
{
    // Base64 encode
    static public function encode($data) {
        return str_replace("\/", "/", json_encode($data));
    }
}
