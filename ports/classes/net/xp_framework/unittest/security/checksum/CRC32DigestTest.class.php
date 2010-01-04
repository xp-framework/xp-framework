<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'net.xp_framework.unittest.security.checksum.AbstractDigestTest',
    'security.checksum.CRC32Digest'
  );

  /**
   * TestCase for CRC32 digest
   *
   * @see      xp://security.checksum.CRC32Digest
   */
  class CRC32DigestTest extends AbstractDigestTest {
  
    /**
     * Creates a new message digest object
     *
     * @return  security.checksum.MessageDigest
     */
    protected function newDigest() {
      return new CRC32Digest();
    }
    
    /**
     * Returns a checksum for a given input string
     *
     * @param   string data
     * @return  security.checksum.Checksum
     */
    protected function checksumOf($data) {
      return CRC32::fromString($data);
    }
  }
?>
