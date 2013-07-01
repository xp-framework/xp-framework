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
   */
  #[@action(class= 'unittest.actions.ExtensionAvailable', args= array('openssl'))]
  class OpenSSLSecureStringTest extends SecureStringTest {

    /**
     * Sets up tests and forces SecureString to use OPENSSL backing
     */
    public function setUp() {
      SecureString::useBacking(SecureString::BACKING_OPENSSL);
    }
  }
?>