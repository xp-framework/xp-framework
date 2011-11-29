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
  class BlowFishUnixCryptTest extends UnixCryptTest {
  
    /**
     * Returns fixture
     *
     * @return  security.crypto.CryptImpl
     */
    protected function fixture() {
      return UnixCrypt::$BLOWFISH;
    }

    /**
     * Test blowfish method
     *
     */
    #[@test]
    public function blowfishPhpNetExample() {
      $this->assertCryptedMatches('$2a$07$usesomesillystringforsalt$', '$2a$07$usesomesillystringfore2uDLvp1Ii2e./U9C8sBjqp8I90dH6hi', 'rasmuslerdorf');
    }

    /**
     * Test blowfish method
     *
     */
    #[@test]
    public function blowfishSaltOneTooLong() {
      $this->assertCryptedMatches('$2a$07$usesomesillystringforsalt$X', '$2a$07$usesomesillystringforeHbE8dX9jg7DlVE.rTNXDHM0HKhUj402');
    }

    /**
     * Test blowfish method
     *
     */
    #[@test]
    public function blowfishSaltOneTooShort() {
      $this->assertCryptedMatches('$2a$07$usesomesillystringforsal', '$2a$07$usesomesillystringforeHbE8dX9jg7DlVE.rTNXDHM0HKhUj402');
    }


    /**
     * Test blowfish method
     *
     */
    #[@test]
    public function blowfishSaltDoesNotEndWithDollar() {
      $this->assertCryptedMatches('$2a$07$usesomesillystringforsalt_', '$2a$07$usesomesillystringforeHbE8dX9jg7DlVE.rTNXDHM0HKhUj402');
    }
  
    /**
     * Test blowfish method
     *
     */
    #[@test, @expect('security.crypto.CryptoException')]
    public function blowfishCostParameterTooShort() {
      $this->fixture()->crypt('irrelevant', '$2a$_');
    }

    /**
     * Test blowfish method
     *
     */
    #[@test, @expect('security.crypto.CryptoException')]
    public function blowfishCostParameterZero() {
      $this->fixture()->crypt('irrelevant', '$2a$00$');
    }

    /**
     * Test blowfish method
     *
     */
    #[@test, @expect('security.crypto.CryptoException')]
    public function blowfishCostParameterTooLow() {
      $this->fixture()->crypt('irrelevant', '$2a$03$');
    }

    /**
     * Test blowfish method
     *
     */
    #[@test, @expect('security.crypto.CryptoException')]
    public function blowfishCostParameterTooHigh() {
      $this->fixture()->crypt('irrelevant', '$2a$32$');
    }

    /**
     * Test blowfish method
     *
     */
    #[@test, @expect('security.crypto.CryptoException')]
    public function blowfishCostParameterMalFormed() {
      $this->fixture()->crypt('irrelevant', '$2a$__$');
    }
  }
?>
