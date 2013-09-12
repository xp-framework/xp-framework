<?php namespace net\xp_framework\unittest\security;

use security\SecureString;


/**
 * Testcase for openssl backed security.SecureString implementation
 *
 */
class OpenSSLSecureStringTest extends SecureStringTest {

  public function setUp() {
    if (!\lang\Runtime::getInstance()->extensionAvailable('openssl')) {
      throw new \unittest\PrerequisitesNotMetError('Needs extension "openssl"');
    }

    SecureString::useBacking(SecureString::BACKING_OPENSSL);
  }
}
