<?php
/*
 * This class is part of the XP Framework
 *
 */

  uses(
    'net.xp_framework.unittest.security.SecureStringTest',
    'security.SecureString'
  );

  class McryptSecureStringTest extends SecureStringTest {

    public function setUp() {
      if (!Runtime::getInstance()->extensionAvailable('mcrypt')) {
        throw new PrerequisitesNotMetError('Needs extension "mcrypt"');
      }

      SecureString::useBacking(SecureString::BACKING_MCRYPT);
    }
  }
?>