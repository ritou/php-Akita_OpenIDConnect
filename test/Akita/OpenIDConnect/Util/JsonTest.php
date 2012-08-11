<?php

require_once dirname(__FILE__) . '/../../../../src/Akita/OpenIDConnect/Util/Json.php';

class Akita_OpenIDConnect_Util_Json_Test 
    extends PHPUnit_Framework_TestCase
{
    public function testEncode()
    {
        $data = array('url'=>'https://openidconnect.info');
        $exepted_enc = '{"url":"https://openidconnect.info"}';
        $enc = Akita_OpenIDConnect_Util_Json::encode($data);
        $this->assertEquals($exepted_enc, $enc);
    }
}
