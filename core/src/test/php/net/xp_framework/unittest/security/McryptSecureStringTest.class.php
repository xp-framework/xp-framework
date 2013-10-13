<?php namespace net\xp_framework\unittest\security;

use security\SecureString;
use unittest\actions\ExtensionAvailable;

/**
 * Testcase for mcrypt backed security.SecureString implementation
 */
#[@action(new ExtensionAvailable('mcrypt'))]
class McryptSecureStringTest extends SecureStringTest {

  /**
   * Use MCRYPT backing
   */
  public function setUp() {
    SecureString::useBacking(SecureString::BACKING_MCRYPT);
  }
}
