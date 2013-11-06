<?php namespace net\xp_framework\unittest\security;

use security\KeyPair;
use security\cert\CSR;
use security\cert\X509Certificate;
use security\crypto\PublicKey;
use security\crypto\PrivateKey;
use unittest\TestCase;

/**
 * Testcase for Public/Private key classes
 *
 * @see   xp://security.crypto.PublicKey
 * @see   xp://security.crypto.PrivateKey
 */
#[@action(new \unittest\actions\ExtensionAvailable('openssl'))]
class CryptoKeyTest extends TestCase {
  public
    $publickey    = null,
    $privatekey   = null,
    $cert         = null;

  /**
   * Setup test environment
   *
   */
  public function setUp() {
    if ($this->cert && $this->publickey && $this->privatekey) return;
    
    // Generate private & public key, using a self-signed certificate
    $keypair= KeyPair::generate();
    $privatekey= $keypair->getPrivateKey();
    
    $csr= new CSR(new \security\Principal(array(
      'C'     => 'DE',
      'ST'    => 'Baden-Württemberg',
      'L'     => 'Karlsruhe',
      'O'     => 'XP',
      'OU'    => 'XP Team',
      'CN'    => 'XP Unittest',
      'EMAIL' => 'unittest@xp-framework.net'
    )), $keypair);
    
    $cert= $csr->sign($keypair);
    $publickey= $cert->getPublicKey();
    $this->cert= $cert;
    $this->publickey= $publickey;
    $this->privatekey= $privatekey;
  }
  
  #[@test]
  public function generateKeys() {
    $this->assertTrue($this->cert->checkPrivateKey($this->privatekey));
  }

  #[@test]
  public function testSignature() {
    $signature= $this->privatekey->sign('This is just some testdata');
    
    $this->assertTrue($this->publickey->verify('This is just some testdata', $signature));
    $this->assertFalse($this->publickey->verify('This is just fake testdata', $signature));
  }
  
  #[@test]
  public function testEncryptionWithPublickey() {
    $crypt= $this->publickey->encrypt('This is the secret data.');
    $this->assertEquals('This is the secret data.', $this->privatekey->decrypt($crypt));
  }    

  #[@test]
  public function testEncryptionWithPrivatekey() {
    $crypt= $this->privatekey->encrypt('This is the secret data.');
    $this->assertEquals('This is the secret data.', $this->publickey->decrypt($crypt));
  }
  
  #[@test]
  public function testSeals() {
    list($data, $key)= $this->publickey->seal('This is my secret data.');
    $this->assertEquals($this->privatekey->unseal($data, $key));
  }    
}
