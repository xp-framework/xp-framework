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
   * Testcase for plaintext backed security.SecureString implementation
   */
  class PlainTextSecureStringTest extends SecureStringTest {

    /**
     * Sets up tests and forces SecureString to use PLAINTEXT backing
     */
    public function setUp() {
      SecureString::useBacking(SecureString::BACKING_PLAINTEXT);
    }
  }
?>