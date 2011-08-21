<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_framework.unittest.security.UnixCryptTest');

  /**
   * TestCase
   *
   * @see   xp://security.crypto.UnixCrypt
   */
  class StandardDESUnixCryptTest extends UnixCryptTest {
  
    /**
     * Returns fixture
     *
     * @return  security.crypto.CryptImpl
     */
    protected function fixture() {
      return UnixCrypt::$STANDARD;
    }

    /**
     * Test traditional method
     *
     */
    #[@test]
    public function traditional() {
      $this->assertCryptedMatches('ab', 'ab54209Hrroig');
    }
  }
?>
