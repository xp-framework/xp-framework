<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'security.KeyPair',
    'security.cert.CSR',
    'security.cert.X509Certificate',
    'security.crypto.PublicKey',
    'security.crypto.PrivateKey',
    'unittest.TestCase'
  );

  /**
   * Testcase for Public/Private key classes
   *
   * @ext       openssl
   * @see       xp://security.crypto.PublicKey
   * @see       xp://security.crypto.PrivateKey
   * @purpose   Testcase
   */
  class CryptoKeyTest extends TestCase {
    public
      $publickey    = NULL,
      $privatekey   = NULL,
      $cert         = NULL;

    /**
     * Setup test environment
     *
     */
    public function setUp() {
      if (!extension_loaded('openssl')) {
        throw new PrerequisitesNotMetError(
          PREREQUISITE_LIBRARYMISSING, 
          $cause= NULL, 
          array('openssl')
        );
      }
      
      if ($this->cert && $this->publickey && $this->privatekey) return;
      
      // Generate private & public key, using a self-signed certificate
      $keypair= KeyPair::generate();
      $privatekey= $keypair->getPrivateKey();
      
      $csr= new CSR(new Principal(array(
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
    
    /**
     * Test validity of generated keys/certificate
     *
     */
    #[@test]
    public function generateKeys() {
      $this->assertTrue($this->cert->checkPrivateKey($this->privatekey));
    }

    /**
     * Test creation of signatures
     *
     */
    #[@test]
    public function testSignature() {
      $signature= $this->privatekey->sign('This is just some testdata');
      
      $this->assertTrue($this->publickey->verify('This is just some testdata', $signature));
      $this->assertFalse($this->publickey->verify('This is just fake testdata', $signature));
    }
    
    /**
     * Test public key encryption
     *
     */
    #[@test]
    public function testEncryptionWithPublickey() {
      $crypt= $this->publickey->encrypt('This is the secret data.');
      
      $this->assertEquals('This is the secret data.', $this->privatekey->decrypt($crypt));
    }    

    /**
     * Test private key encryption
     *
     */
    #[@test]
    public function testEncryptionWithPrivatekey() {
      $crypt= $this->privatekey->encrypt('This is the secret data.');
      
      $this->assertEquals('This is the secret data.', $this->publickey->decrypt($crypt));
    }
    
    /**
     * Test seals.
     *
     */
    #[@test]
    public function testSeals() {
      list($data, $key)= $this->publickey->seal('This is my secret data.');
      
      $this->assertEquals($this->privatekey->unseal($data, $key));
    }    
  }
?>
