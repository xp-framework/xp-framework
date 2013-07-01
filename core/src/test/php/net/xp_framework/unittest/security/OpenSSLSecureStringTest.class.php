<?php
/* This class is part of the XP Framework
 *
 * $Id$
 */

  uses(
    'net.xp_framework.unittest.security.SecureStringTest',
    'security.SecureString'
  );

  /**
   * Testcase for openssl backed security.SecureString implementation
   *
   */
  class OpenSSLSecureStringTest extends SecureStringTest {

    public function setUp() {
      if (!Runtime::getInstance()->extensionAvailable('openssl')) {
        throw new PrerequisitesNotMetError('Needs extension "openssl"');
      }

      SecureString::useBacking(SecureString::BACKING_OPENSSL);
    }
  }
?>