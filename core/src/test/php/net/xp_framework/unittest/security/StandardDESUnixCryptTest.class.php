<?php namespace net\xp_framework\unittest\security;



/**
 * TestCase
 *
 * @see   xp://security.crypto.UnixCrypt
 */
class StandardDESUnixCryptTest extends UnixCryptTest {

  /**
   * Returns fixture
   *
   * @return  security.crypto.CryptImpl
   */
  protected function fixture() {
    return \security\crypto\UnixCrypt::$STANDARD;
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
   * Test traditional method
   *
   */
  #[@test]
  public function saltTooLong() {
    $this->assertCryptedMatches('abc', 'ab54209Hrroig');
  }

  /**
   * Test traditional method
   *
   */
  #[@test]
  public function saltTooShort() {
    try {
      $this->assertCryptedMatches('a', 'a$Xz1wsurHC5M');
    } catch (\security\crypto\CryptoException $ignored) { }
  }

  /**
   * Test salt with $2$
   *
   */
  #[@test]
  public function saltWayTooLong() {
    $this->assertCryptedMatches('0123456789ABCDEF', '01f./qIYmRW1Y');
  }

  /**
   * Test salt with 1$ in the beginning
   *
   * @see   http://bugs.debian.org/cgi-bin/bugreport.cgi?bug=572601
   */
  #[@test]
  public function oneDollar() {
    try {
      $this->assertCryptedMatches('1$', '1$SyvOllpoCvg');
    } catch (\security\crypto\CryptoException $ignored) { }
  }

  /**
   * Test salt with $2 in the beginning
   *
   */
  #[@test]
  public function dollarTwo() {
    try {
      $this->assertCryptedMatches('$2', '$26WPvCItMuNE');
    } catch (\security\crypto\CryptoException $ignored) { }
  }

  /**
   * Test salt with $$ in the beginning
   *
   */
  #[@test]
  public function dollarDollar() {
    try {
      $this->assertCryptedMatches('$$', '$$oLnFl.kOCXI');
    } catch (\security\crypto\CryptoException $ignored) { }
  }

  /**
   * Test salt with unsafe character "\n"
   *
   * @see   http://lxr.php.net/xref/PHP_5_3/ext/standard/crypt_freesec.c#ascii_is_unsafe
   */
  #[@test, @expect('security.crypto.CryptoException')]
  public function unsafeLineFeed() {
    $this->fixture()->crypt('irrelevant', "\n_");
  }

  /**
   * Test salt with unsafe character ":"
   *
   * @see   http://lxr.php.net/xref/PHP_5_3/ext/standard/crypt_freesec.c#ascii_is_unsafe
   */
  #[@test, @expect('security.crypto.CryptoException')]
  public function unsafeColon() {
    $this->fixture()->crypt('irrelevant', ':_');
  }
}
