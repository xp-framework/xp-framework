<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'net.xp_framework.unittest.security.checksum.AbstractDigestTest',
    'security.checksum.CRC32'
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
     * @return  security.checksum.MessageDigestImpl
     */
    protected function newDigest() {
      return CRC32::digest();
    }
    
    /**
     * Returns a checksum for a given input string
     *
     * @param   string data
     * @return  security.checksum.Checksum
     */
    protected function checksumOf($data) {
      return CRC32::fromString($data)->getValue();
    }
  }
?>
