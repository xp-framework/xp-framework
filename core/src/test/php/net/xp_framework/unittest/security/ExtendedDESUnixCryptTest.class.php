<?php namespace net\xp_framework\unittest\security;



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
    return \security\crypto\UnixCrypt::$EXTENDED;
  }

  /**
   * Test extended DES method
   *
   */
  #[@test]
  public function extendedDES() {
    $this->assertCryptedMatches('_12345678', '_12345678SkhUrQrtUJM');
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
  public function extendedDES1CharSalt() {
    $this->fixture()->crypt('plain', '_');
  }

  /**
   * Test extended DES method
   *
   */
  #[@test, @expect('security.crypto.CryptoException')]
  public function extendedDES2CharSalt() {
    $this->fixture()->crypt('plain', '_1');
  }

  /**
   * Test extended DES method
   *
   */
  #[@test, @expect('security.crypto.CryptoException')]
  public function extendedDES7CharSalt() {
    $this->fixture()->crypt('plain', '_1234567');
  }
}
