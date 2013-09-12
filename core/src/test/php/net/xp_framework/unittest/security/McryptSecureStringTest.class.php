<?php namespace net\xp_framework\unittest\security;

use security\SecureString;


/**
 * Testcase for mcrypt backed security.SecureString implementation
 *
 */
class McryptSecureStringTest extends SecureStringTest {

  public function setUp() {
    if (!\lang\Runtime::getInstance()->extensionAvailable('mcrypt')) {
      throw new \unittest\PrerequisitesNotMetError('Needs extension "mcrypt"');
    }

    SecureString::useBacking(SecureString::BACKING_MCRYPT);
  }
}
