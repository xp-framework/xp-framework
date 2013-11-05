<?php namespace net\xp_framework\unittest\security;

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
    return \security\crypto\UnixCrypt::$BLOWFISH;
  }

  #[@test]
  public function blowfishPhpNetExample() {
    $this->assertCryptedMatches('$2a$07$usesomesillystringforsalt$', '$2a$07$usesomesillystringfore2uDLvp1Ii2e./U9C8sBjqp8I90dH6hi', 'rasmuslerdorf');
  }

  #[@test]
  public function blowfishSaltOneTooLong() {
    $this->assertCryptedMatches('$2a$07$usesomesillystringforsalt$X', '$2a$07$usesomesillystringforeHbE8dX9jg7DlVE.rTNXDHM0HKhUj402');
  }

  #[@test]
  public function blowfishSaltOneTooShort() {
    $this->assertCryptedMatches('$2a$07$usesomesillystringforsal', '$2a$07$usesomesillystringforeHbE8dX9jg7DlVE.rTNXDHM0HKhUj402');
  }


  #[@test]
  public function blowfishSaltDoesNotEndWithDollar() {
    $this->assertCryptedMatches('$2a$07$usesomesillystringforsalt_', '$2a$07$usesomesillystringforeHbE8dX9jg7DlVE.rTNXDHM0HKhUj402');
  }

  #[@test, @expect('security.crypto.CryptoException')]
  public function blowfishCostParameterTooShort() {
    $this->fixture()->crypt('irrelevant', '$2a$_');
  }

  #[@test, @expect('security.crypto.CryptoException')]
  public function blowfishCostParameterZero() {
    $this->fixture()->crypt('irrelevant', '$2a$00$');
  }

  #[@test, @expect('security.crypto.CryptoException')]
  public function blowfishCostParameterTooLow() {
    $this->fixture()->crypt('irrelevant', '$2a$03$');
  }

  #[@test, @expect('security.crypto.CryptoException')]
  public function blowfishCostParameterTooHigh() {
    $this->fixture()->crypt('irrelevant', '$2a$32$');
  }

  #[@test, @expect('security.crypto.CryptoException')]
  public function blowfishCostParameterMalFormed() {
    $this->fixture()->crypt('irrelevant', '$2a$__$');
  }
}
