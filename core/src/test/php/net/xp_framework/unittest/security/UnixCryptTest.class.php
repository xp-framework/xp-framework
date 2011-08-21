<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'security.crypto.UnixCrypt'
  );

  /**
   * TestCase
   *
   * @see   xp://security.crypto.UnixCrypt
   */
  class UnixCryptTest extends TestCase {
  
    /**
     * Assertion helper
     *
     * @param   string salt
     * @param   string crypted
     * @param   string plain default 'plain'
     * @throws  unittest.AssertionFailedError
     */
    protected function assertCryptedMatches($salt, $crypted, $plain= 'plain') {
      $this->assertEquals($crypted, UnixCrypt::crypt($plain, $salt), 'Crypted string not equal');
      $this->assertTrue(UnixCrypt::matches($crypted, $plain), 'Entered does not match crypted');
      $this->assertFalse(UnixCrypt::matches($crypted, 'other'), 'Incorrect value matches crypted');
    }
  
    /**
     * Test traditional method
     *
     */
    #[@test]
    public function traditional() {
      $this->assertCryptedMatches('ab', 'ab54209Hrroig');
    }

    /**
     * Test MD5 method
     *
     */
    #[@test]
    public function md5() {
      $this->assertCryptedMatches('$1$0123456789AB', '$1$01234567$CEE8q9mw43U6PHo8uPcOW/');
    }

    /**
     * Test MD5 method
     *
     */
    #[@test]
    public function md5PhpNetExample() {
      $this->assertCryptedMatches('$1$rasmusle$', '$1$rasmusle$rISCgZzpwk3UhDidwXvin0', 'rasmuslerdorf');
    }

    /**
     * Test MD5 method
     *
     */
    #[@test]
    public function md5ShortSalt() {
      $this->assertCryptedMatches('$1$_', '$1$_$.m3t.Z4nwsU9NHyuqbRAC1');
    }

    /**
     * Test blowfish method
     *
     */
    #[@test]
    public function blowfish() {
      $this->assertCryptedMatches('$2$0123456789ABCDEF', '$26WPvCItMuNE');
    }

    /**
     * Test extended DES method
     *
     */
    #[@test]
    public function extendedDES() {
      $this->assertCryptedMatches('_012345678', '_01234567xl8NJKKN6es');
    }

    /**
     * Test extended DES method
     *
     */
    #[@test, @expect('security.crypto.CryptoException')]
    public function extendedDESSaltTooShort() {
      UnixCrypt::crypt('plain', '_');
    }

    /**
     * Test MD5 method
     *
     */
    #[@test]
    public function phpBug55439() {
      $this->assertEquals(
        '$1$U7AjYB.O$L1N7ux7twaMIMw0En8UUR1',
        UnixCrypt::crypt('password', '$1$U7AjYB.O$')
      );
    }
  }
?>
