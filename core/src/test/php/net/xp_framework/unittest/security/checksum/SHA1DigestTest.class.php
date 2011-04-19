<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'net.xp_framework.unittest.security.checksum.AbstractDigestTest',
    'security.checksum.SHA1'
  );

  /**
   * TestCase for SHA1 digest
   *
   * @see      xp://security.checksum.SHA1Digest
   */
  class SHA1DigestTest extends AbstractDigestTest {
  
    /**
     * Creates a new message digest object
     *
     * @return  security.checksum.MessageDigest
     */
    protected function newDigest() {
      return SHA1::digest();
    }
    
    /**
     * Returns a checksum for a given input string
     *
     * @param   string data
     * @return  security.checksum.Checksum
     */
    protected function checksumOf($data) {
      return SHA1::fromString($data)->getValue();
    }
  }
?>
