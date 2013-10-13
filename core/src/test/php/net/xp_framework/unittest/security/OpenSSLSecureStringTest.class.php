<?php namespace net\xp_framework\unittest\security;

use security\SecureString;

/**
 * Testcase for openssl backed security.SecureString implementation
 */
#[@action(class= 'unittest.actions.ExtensionAvailable', args= array('openssl'))]
class OpenSSLSecureStringTest extends SecureStringTest {

  /**
   * Use OPENSSL backing
   */
  public function setUp() {
    SecureString::useBacking(SecureString::BACKING_OPENSSL);
  }
}
