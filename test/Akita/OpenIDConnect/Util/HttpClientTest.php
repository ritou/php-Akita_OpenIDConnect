<?php

require_once dirname(__FILE__) . '/../../../../src/Akita/OpenIDConnect/Util/HttpClient.php';

class Akita_OpenIDConnect_Util_HttpClient_Test 
    extends PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $httpClient = new Akita_OpenIDConnect_Util_HttpClient();
        $result = $httpClient->get('http://www.yahoo.co.jp/');
        $this->assertNotEquals(false, $result);
        $result = $httpClient->get('https://www.google.co.jp/');
        $this->assertNotEquals(false, $result);
        $httpClient->setTimeout(20, 20);
        $result = $httpClient->get('https://openidconnect.info/');
        $this->assertNotEquals(false, $result);
    }
}
