<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'unittest.TestCase',
    'security.cert.X509Certificate'
  );

  /**
   * Test certificate code
   *
   * @purpose  Unit Test
   */
  class CertificateTest extends TestCase {
    protected
      $fixture  = NULL;

    /**
     * Set up this testcase
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
      
      $this->fixture= X509Certificate::fromString(trim('
-----BEGIN CERTIFICATE-----
MIICtDCCAh2gAwIBAwIBADANBgkqhkiG9w0BAQQFADCBnzELMAkGA1UEBhMCREUx
GjAYBgNVBAgUEUJhZGVuLVf8cnR0ZW1iZXJnMRIwEAYDVQQHEwlLYXJsc3J1aGUx
EDAOBgNVBAoTB1hQIFRlYW0xEDAOBgNVBAsTB1hQIFRlYW0xFDASBgNVBAMTC1Rp
bW0gRnJpZWJlMSYwJAYJKoZIhvcNAQkBFhdmcmllYmVAeHAtZnJhbWV3b3JrLm5l
dDAeFw0wMzAyMDkxNTE2NDlaFw0wNDAyMDkxNTE2NDlaMIGfMQswCQYDVQQGEwJE
RTEaMBgGA1UECBQRQmFkZW4tV/xydHRlbWJlcmcxEjAQBgNVBAcTCUthcmxzcnVo
ZTEQMA4GA1UEChMHWFAgVGVhbTEQMA4GA1UECxMHWFAgVGVhbTEUMBIGA1UEAxML
VGltbSBGcmllYmUxJjAkBgkqhkiG9w0BCQEWF2ZyaWViZUB4cC1mcmFtZXdvcmsu
bmV0MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDHg6T7V45CAtsDsJ4Lw/RT
31SHKqvkofbCzaREJ0yg8fy2KtmurU55JK5VOmcdFPIAgtEo3qaCXUtUfRVS398O
ezwkcOmJRhbkkzUGiuGbRobZRLjOXrYCQYZ7mQJQc80wmki0SLy0OmU1SrJiWIBy
UoOjW4EQZqVCuEHgeRiAdwIDAQABMA0GCSqGSIb3DQEBBAUAA4GBADeL3Pvtua3w
nwdr2RRfQ3f1b36gRN3loSiEspDhCjbdR6xf//r+/XewPtP86HSx+hEKuwkNh+oY
UnoNtLoDwBRZkrJIvOyuzBwaMIlLvYGfGYr3DAweMqn3AQ2j5GaA56cMrVa+Tb/y
WPDyiSAwwKIzRnlGBb+eJGQX2ZDyvPg7
-----END CERTIFICATE-----
      '));      
    }
    
    /**
     * Assertion helper
     *
     * @param   string pattern
     * @param   security.Principal p
     * @param   string cause
     * @throws  unittest.AssertionFailedError
     */
    protected function assertPrincipal($pattern, Principal $p, $cause) {
      if (!preg_match($pattern, $p->getName())) {
        $this->fail($cause.' did not match', $p->getName(), $pattern);
      }
    }
    
    /**
     * Test an X.509 certificate
     *
     */
    #[@test]
    public function subjectDN() {
      $this->assertPrincipal(
        '#^/C=DE/ST=Baden-Württemberg/L=Karlsruhe/O=XP Team/OU=XP Team/CN=Timm Friebe/EMAIL(ADDRESS)?=friebe@xp-framework.net$#', 
        $this->fixture->getSubjectDN(), 
        'subject'
      );
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function version() {
      $this->assertEquals(3, $this->fixture->getVersion());
    }
      
    /**
     * Test
     *
     */
    #[@test]
    public function issuerDN() {
      $this->assertPrincipal(
        '#^/C=DE/ST=Baden-Württemberg/L=Karlsruhe/O=XP Team/OU=XP Team/CN=Timm Friebe/EMAIL(ADDRESS)?=friebe@xp-framework.net$#', 
        $this->fixture->getIssuerDN(), 
        'issuer'
      );
    }
    
    /**
     * Test
     *
     * @see    http://www.cilogon.org/openssl1 Hashing changed in OpenSSL 1.0.0
     */
    #[@test]
    public function hashMethod() {
      $this->assertEquals(
        OPENSSL_VERSION_NUMBER < 0x10000000 ? 'f2473bfa' : '2f76c690', 
        $this->fixture->getHash(), 
        'hash'
      );
    }
  }
?>
