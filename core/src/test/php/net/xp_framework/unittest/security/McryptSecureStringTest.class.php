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
   */
  #[@action(class= 'unittest.actions.ExtensionAvailable', args= array('mcrypt'))]
  class McryptSecureStringTest extends SecureStringTest {

    /**
     * Sets up tests and forces SecureString to use MCRYPT backing
     */
    public function setUp() {
      SecureString::useBacking(SecureString::BACKING_MCRYPT);
    }
  }
?>