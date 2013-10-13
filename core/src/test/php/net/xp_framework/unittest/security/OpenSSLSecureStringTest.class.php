<?php namespace net\xp_framework\unittest\security;

use security\SecureString;
use unittest\actions\ExtensionAvailable;

/**
 * Testcase for openssl backed security.SecureString implementation
 */
#[@action(new ExtensionAvailable('openssl'))]
class OpenSSLSecureStringTest extends SecureStringTest {

  /**
   * Use OPENSSL backing
   */
  public function setUp() {
    SecureString::useBacking(SecureString::BACKING_OPENSSL);
  }
}
