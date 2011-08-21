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
  class ExtendedDESUnixCryptTest extends UnixCryptTest {
  
    /**
     * Returns fixture
     *
     * @return  security.crypto.CryptImpl
     */
    protected function fixture() {
      return UnixCrypt::$EXTENDED;
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
    #[@test]
    public function extendedDESPhpNetExample() {
      $this->assertCryptedMatches('_J9..rasm', '_J9..rasmBYk8r9AiWNc', 'rasmuslerdorf');
    }

    /**
     * Test extended DES method
     *
     */
    #[@test, @expect('security.crypto.CryptoException')]
    public function extendedDESSaltTooShort() {
      $this->fixture()->crypt('plain', '_');
    }

  }
?>
