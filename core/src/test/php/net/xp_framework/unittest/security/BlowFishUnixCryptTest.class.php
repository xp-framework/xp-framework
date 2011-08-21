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
  class BlowFishUnixCryptTest extends UnixCryptTest {
  
    /**
     * Returns fixture
     *
     * @return  security.crypto.CryptImpl
     */
    protected function fixture() {
      return UnixCrypt::$BLOWFISH;
    }

  
    /**
     * Test blowfish method
     *
     */
    #[@test]
    public function blowfish() {
      $this->assertCryptedMatches('$2$0123456789ABCDEF', '$26WPvCItMuNE');
    }
  }
?>
