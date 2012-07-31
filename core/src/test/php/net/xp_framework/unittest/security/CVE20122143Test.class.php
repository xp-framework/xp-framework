<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase', 'security.crypto.UnixCrypt');

  /**
   * TestCase
   *
   * @see   xp://security.crypto.UnixCrypt
   * @see   http://web.nvd.nist.gov/view/vuln/detail?vulnId=CVE-2012-2143
   * @see   http://www.php.net/archive/2012.php#id2012-06-14-1
   */
  class CVE20122143Test extends TestCase {

    /**
     * Only run these tests for PHP 5.3.14+ and PHP 5.4.4+
     *
     */
    public function setUp() {
      if (!(version_compare(PHP_VERSION, '5.4.4', 'ge') || (
        version_compare(PHP_VERSION, '5.3.14', 'ge') &&
        version_compare(PHP_VERSION, '5.4.0', 'lt')
      ))) {
        throw new PrerequisitesNotMetError('Not for PHP '.PHP_VERSION, NULL, array('[5.3.14..5.4.0[', '5.4.4+'));
      }
    }

    /**
     * Assertion helper
     *
     */
    protected function assertCrypt($crypt, $expected, $plain, $salt) {

      // If the crypt method is not implemented, succeed, the algorithm cannot
      // be affected. Better would be to mark the test skipped, but the test
      // suite doesn't allow for this.
      if ($crypt instanceof CryptNotImplemented) return TRUE;
      
      $this->assertEquals($expected, $crypt->crypt($plain, $salt));
    }

    /**
     * Test variant #1
     *
     */
    #[@test]
    public function std_variant_1() {
      $this->assertCrypt(UnixCrypt::$STANDARD, '99PxawtsTfX56', 'À1234abcd', '99');
    }

    /**
     * Test variant #2
     *
     */
    #[@test]
    public function std_variant_2() {
      $this->assertCrypt(UnixCrypt::$STANDARD, '99jcVcGxUZOWk', 'À9234abcd', '99');
    }

    /**
     * Test variant #1
     *
     */
    #[@test]
    public function ext_variant_1() {
      $this->assertCrypt(UnixCrypt::$EXTENDED, '_01234567IBjxKliXXRQ', 'À1234abcd', '_01234567');
    }

    /**
     * Test variant #2
     *
     */
    #[@test]
    public function ext_variant_2() {
      $this->assertCrypt(UnixCrypt::$EXTENDED, '_012345678OSGpGQRVHA', 'À9234abcd', '_01234567');
    }
  }
?>
