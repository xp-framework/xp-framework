<?php namespace net\xp_framework\unittest\security;

use security\SecureString;

/**
 * Testcase for plaintext backed security.SecureString implementation
 */
class PlainTextSecureStringTest extends SecureStringTest {

  /**
   * Use PLAINTEXT backing
   */
  public function setUp() {
    SecureString::useBacking(SecureString::BACKING_PLAINTEXT);
  }
}
