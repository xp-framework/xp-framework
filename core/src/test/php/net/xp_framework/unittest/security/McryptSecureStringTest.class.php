<?php namespace net\xp_framework\unittest\security;

use security\SecureString;

/**
 * Testcase for mcrypt backed security.SecureString implementation
 */
#[@action(class= 'unittest.actions.ExtensionAvailable', args= array('mcrypt'))]
class McryptSecureStringTest extends SecureStringTest {

  /**
   * Use MCRYPT backing
   */
  public function setUp() {
    SecureString::useBacking(SecureString::BACKING_MCRYPT);
  }
}
