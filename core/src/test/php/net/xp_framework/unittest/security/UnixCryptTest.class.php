<?php namespace net\xp_framework\unittest\security;

use unittest\TestCase;
use security\crypto\UnixCrypt;


/**
 * TestCase
 *
 * @see   xp://security.crypto.UnixCrypt
 */
abstract class UnixCryptTest extends TestCase {
  protected $fixture= null;

  /**
   * Returns fixture
   *
   * @return  security.crypto.CryptImpl
   */
  protected abstract function fixture();
  
  /**
   * Setup test
   *
   */
  public function setUp() {
    $this->fixture= $this->fixture();
    if ($this->fixture instanceof \security\crypto\CryptNotImplemented) {
      throw new \unittest\PrerequisitesNotMetError('Not implemented');
    }
  }
  
  /**
   * Assertion helper
   *
   * @param   string salt
   * @param   string crypted
   * @param   string plain default 'plain'
   * @throws  unittest.AssertionFailedError
   */
  protected function assertCryptedMatches($salt, $crypted, $plain= 'plain') {
    $this->assertEquals($crypted, $this->fixture->crypt($plain, $salt), 'Crypted string not equal');
    $this->assertTrue($this->fixture->matches($crypted, $plain), 'Entered does not match crypted');
    $this->assertFalse($this->fixture->matches($crypted, strrev($plain)), 'Incorrect value matches crypted');
  }
}
