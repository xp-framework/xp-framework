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
   * Testcase for mcrypt backed security.SecureString implementation
   *
   */
  class McryptSecureStringTest extends SecureStringTest {

    public function setUp() {
      if (!Runtime::getInstance()->extensionAvailable('mcrypt')) {
        throw new PrerequisitesNotMetError('Needs extension "mcrypt"');
      }

      SecureString::useBacking(SecureString::BACKING_MCRYPT);
    }
  }
?>