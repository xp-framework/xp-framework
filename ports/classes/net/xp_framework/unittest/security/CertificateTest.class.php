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
    }
    
    /**
     * Test an X.509 certificate
     *
     */
    #[@test]
    public function testX509Certificate() {
      $esubject= '#^/C=DE/ST=Baden-Württemberg/L=Karlsruhe/O=XP Team/OU=XP Team/CN=Timm Friebe/EMAIL(ADDRESS)?=friebe@xp-framework.net$#';
      $eissuer= '#^/C=DE/ST=Baden-Württemberg/L=Karlsruhe/O=XP Team/OU=XP Team/CN=Timm Friebe/EMAIL(ADDRESS)?=friebe@xp-framework.net$#';
      $ehash= 'f2473bfa';
      try {
        $x509= X509Certificate::fromString(<<<EOC
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
EOC
);
      } catch (CertificateException $e) {
        return $this->fail($e->getClassName(), $e->getMessage(), 'fromstring');
      }

      $subject= $x509->getSubjectDN();
      $issuer= $x509->getIssuerDN();
      $this->assertTrue((bool)preg_match($esubject, $subject->getName()), 'subject');
      $this->assertTrue((bool)preg_match($eissuer, $issuer->getName()), 'issuer');
      $this->assertEquals($x509->getHash(), $ehash, 'hash');
    }
  }
?>
