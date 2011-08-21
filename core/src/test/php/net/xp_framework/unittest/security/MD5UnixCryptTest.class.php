<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_framework.unittest.security.UnixCryptTest');

  /**
   * TestCase
   *
   * @see   xp://security.crypto.UnixCrypt
   */
  class MD5UnixCryptTest extends UnixCryptTest {
  
    /**
     * Returns fixture
     *
     * @return  security.crypto.CryptImpl
     */
    protected function fixture() {
      return UnixCrypt::$MD5;
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
    public function md5SaltTooLong() {
      $this->assertCryptedMatches('$1$0123456789AB', '$1$01234567$CEE8q9mw43U6PHo8uPcOW/');
    }

    /**
     * Test MD5 method
     *
     */
    #[@test]
    public function md5SaltTooShort() {
      $this->assertCryptedMatches('$1$_', '$1$_$.m3t.Z4nwsU9NHyuqbRAC1');
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
