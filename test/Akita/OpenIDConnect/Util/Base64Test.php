<?php

require_once dirname(__FILE__) . '/../../../../src/Akita/OpenIDConnect/Util/Base64.php';

class Akita_OpenIDConnect_Util_Base64_Test 
    extends PHPUnit_Framework_TestCase
{
    public function testUrlEncode()
    {
        $str = "1";
        $enc_str = "MQ";
        $enc = Akita_OpenIDConnect_Util_Base64::urlEncode($str);
        $this->assertEquals($enc_str, $enc);

        $str = "1234";
        $enc_str = "MTIzNA";
        $enc = akita_openidconnect_util_base64::urlEncode($str);
        $this->assertequals($enc_str, $enc);

        $str = "ABCDEFG";
        $enc_str = "QUJDREVGRw";
        $enc = akita_openidconnect_util_base64::urlEncode($str);
        $this->assertequals($enc_str, $enc);
    }

    public function testUrlDecode()
    {
        $dec_str = "1";
        $str = "MQ";
        $dec = Akita_OpenIDConnect_Util_Base64::urlDecode($str);
        $this->assertEquals($dec_str, $dec);

        $dec_str = "1234";
        $str = "MTIzNA";
        $dec = akita_openidconnect_util_base64::urlDecode($str);
        $this->assertequals($dec_str, $dec);

        $dec_str = "ABCDEFG";
        $str = "QUJDREVGRw";
        $dec = akita_openidconnect_util_base64::urlDecode($str);
        $this->assertequals($dec_str, $dec);
    }

}
