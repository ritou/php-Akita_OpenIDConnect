<?php

require_once dirname(__FILE__) . '/../../../../../src/Akita/OpenIDConnect/Util/JOSE/JWS.php';

class Akita_OpenIDConnect_Util_JOSE_JWS_Test 
    extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $alg = 'none';
        $jws = new Akita_OpenIDConnect_Util_JOSE_JWS($alg);
        $typ = 'JWS';
        $jws2 = new Akita_OpenIDConnect_Util_JOSE_JWS($alg, $typ);
        $this->assertEquals($jws, $jws2);

        $alg_array = array( 'none', 
                            'HS256', 'HS384', 'HS512', 
                            'RS256', 'RS384', 'RS512', 
                            'ES256', 'ES384', 'ES512');

        foreach($alg_array as $alg){
            try{
                $jws = new Akita_OpenIDConnect_Util_JOSE_JWS($alg);
                $jws2 = new Akita_OpenIDConnect_Util_JOSE_JWS($alg, 'JWS');
                $this->assertEquals($jws, $jws2);
            }catch(Exception $e){
                $this->assertEquals(false, true, $e->getMessage());
            }
        }

        // invalid alg
        $alg = 'invalid';
        try{
                $jws = new Akita_OpenIDConnect_Util_JOSE_JWS($alg);
        }catch(Exception $e){
                $this->assertEquals('Unknown Signature Algorithm', $e->getMessage());
        }
        try{
                $jws = new Akita_OpenIDConnect_Util_JOSE_JWS($alg, 'JWS');
        }catch(Exception $e){
                $this->assertEquals('Unknown Signature Algorithm', $e->getMessage());
        }
        
        // invalid typ
        $alg = 'none';
        $typ = 'JWT';
        try{
                $jws = new Akita_OpenIDConnect_Util_JOSE_JWS($alg, $typ);
        }catch(Exception $e){
                $this->assertEquals(false, true, $e->getMessage());
        }
        $typ = 'JWS';
        try{
                $jws = new Akita_OpenIDConnect_Util_JOSE_JWS($alg, $typ);
        }catch(Exception $e){
                $this->assertEquals(false, true, $e->getMessage());
        }
        $typ = 'INVALID';
        try{
                $jws = new Akita_OpenIDConnect_Util_JOSE_JWS($alg, $typ);
        }catch(Exception $e){
                $this->assertEquals('Unknown typ', $e->getMessage());
        }
    }

    public function testSign()
    {
        // none
        $jws = new Akita_OpenIDConnect_Util_JOSE_JWS('none');
        $dummy_key = 'This is dummy key';
        $signatureBaseString = $jws->getSignatureBaseString();
        $jws->sign($signatureBaseString, $dummy_key);
        $token = $jws->getTokenString();
        $this->assertEquals('eyJhbGciOiJub25lIiwidHlwIjoiSldTIn0..', $token);

        // HSxxx
        $shared_key = 'This is shared key';
        $jws = new Akita_OpenIDConnect_Util_JOSE_JWS('HS256');
        $signatureBaseString = $jws->getSignatureBaseString();
        $jws->sign($signatureBaseString, $shared_key);
        $token = $jws->getTokenString();
        $this->assertEquals('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9..BBHUQEP4sXbbXSltNsitpyhElaIOiuC0D5KyRm5U5ao', $token);

        $jws->setHeaderItem('alg', 'HS384');
        $signatureBaseString = $jws->getSignatureBaseString();
        $jws->sign($signatureBaseString, $shared_key);
        $token = $jws->getTokenString();
        $this->assertEquals('eyJhbGciOiJIUzM4NCIsInR5cCI6IkpXUyJ9..HdUTmRTs5ATJ7GbW-R2uZBOmemBr7VpH3s5Ro735mXaN7X6gBAn44Tw3kAI_alwB', $token);

        $jws->setHeaderItem('alg', 'HS512');
        $signatureBaseString = $jws->getSignatureBaseString();
        $jws->sign($signatureBaseString, $shared_key);
        $token = $jws->getTokenString();
        $this->assertEquals('eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXUyJ9..hoQzFqLadmYQsoszilrtl3uIpBMRzJSP3y7_NLw0UREWVBg2ya-FW36GbwY8dGzp7l3wGKgaDiMvSv7bfNB63Q', $token);

        // RSXXX
        // command for private key generation "openssl genrsa -aes256 -out private.key 2048"
        $passphrase = "Akita_OpenIDConnect_Util_JOSE";
        $private_key = openssl_pkey_get_private("file://".dirname(__FILE__)."/private.key", $passphrase);

        $jws = new Akita_OpenIDConnect_Util_JOSE_JWS('RS256');
        $signatureBaseString = $jws->getSignatureBaseString();
        $jws->sign($signatureBaseString, $private_key);
        $token = $jws->getTokenString();
        $this->assertEquals('eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXUyJ9..OSkKbZavSevIadLDmCksURCPYY9ls6DqbcgqpCNdTtBqHbeqwnfGeBrl9atO-l-JzyRAfCAAXZWysrJjLUKwyflsBoU-y285NerZ2sAQuz7h2NwaeyNZOJas1f7zfL5ldnrbSha3RlffB1dOp6433oqSZhTuZ7deUF1emALP3lZM7zpAq5kxonkPcJ1G_4NSCcBd1DU8AuCYcBqM6QTmNUycPa-wwhs2qWPIXeiLpc9sR8lWZ-PYUH0dbBAJd_D0wE-vcvyVKws8H943ip-6JBSGIVSD78hgrBw7h0DV_ylAuBveBEemtmg95xopQl0b2sEvFlb17z7zoG-Q8apNjQ', $token);

        $jws->setHeaderItem('alg', 'RS384');
        $signatureBaseString = $jws->getSignatureBaseString();
        $jws->sign($signatureBaseString, $private_key);
        $token = $jws->getTokenString();
        $this->assertEquals('eyJhbGciOiJSUzM4NCIsInR5cCI6IkpXUyJ9..flbQttIfeEkXrPE7-WsXZwyIygh1rSg2LCMj9QVINP19jcKfBZya8eP5LzN0AeC1SBeUmTTgoiXvZP92Sq-MUz5GiAmC76ikCpM8_OPIHRo8GhF37ilSQa5j0KJNXsfAkRtDBLpV50UtLMIM3YKeGFE_7dVwAOiZAU5__LTRH85MPljdQ4t6Dq90e-6mX11fjFU_q7q9nSFR9YlHcfBehlszhjRnrSJVvY0Gi9ie05uF591FKXQxSJLF5VrBeoGyVJ4M-D5z7Xczey4wCNBLTpPDGdLms_o_NqCXaW94m2Z21Ea928AzWzBZTygui--IzFgbGnxEXHZDxe9kcVzkng', $token);

        $jws->setHeaderItem('alg', 'RS512');
        $signatureBaseString = $jws->getSignatureBaseString();
        $jws->sign($signatureBaseString, $private_key);
        $token = $jws->getTokenString();
        $this->assertEquals('eyJhbGciOiJSUzUxMiIsInR5cCI6IkpXUyJ9..V5z0xt6M44tnCSbtH6BokrrgzqSn3RWj-BRYGqRK7eTpz8YoF7pMcOjrtVUNzh2tpVKneyEK80_KA53RPAuUjOUp1wsGiU2s524cFyHhPV81TTOEdCRIo2-lH5JVTusxtEyoIARjhICwopi3AAyPNEUZl6jml_UbtMO35J_5MVAcTs6_tHOA37SLiBrb4ZiweSajvLuVk7OhdQhxirQ9PwQEW24rTDzs5YA6raDs6_LcJjP66dHXQ5KLZHkrHMduxtS6KXrbHpoZawbbkfaWsCLXBYNmCcdr5jWdXHRHyimUw8Iktm3dY3lecxSmr5_6TLc-QfNfnINcX17SWhdA2A', $token);
    }

    public function testIsAllowedAlg()
    {
        $alg_array = array( 'none', 
                            'HS256', 'HS384', 'HS512', 
                            'RS256', 'RS384', 'RS512', 
                            'ES256', 'ES384', 'ES512');

        foreach($alg_array as $alg){
            $ret = Akita_OpenIDConnect_Util_JOSE_JWS::isAllowedAlg($alg);
            $this->assertEquals(true, $ret);
        }

        $ret = Akita_OpenIDConnect_Util_JOSE_JWS::isAllowedAlg('invalid');
        $this->assertEquals(false, $ret);
    }
}
