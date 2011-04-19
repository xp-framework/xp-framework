<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'net.xp_framework.unittest.security.checksum.AbstractDigestTest',
    'security.checksum.MD5'
  );

  /**
   * TestCase for MD5 digest
   *
   * @see      xp://security.checksum.MD5Digest
   */
  class MD5DigestTest extends AbstractDigestTest {
  
    /**
     * Creates a new message digest object
     *
     * @return  security.checksum.MessageDigest
     */
    protected function newDigest() {
      return MD5::digest();
    }
    
    /**
     * Returns a checksum for a given input string
     *
     * @param   string data
     * @return  security.checksum.Checksum
     */
    protected function checksumOf($data) {
      return MD5::fromString($data)->getValue();
    }
  }
?>
