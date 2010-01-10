<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'net.xp_framework.unittest.security.checksum.AbstractDigestTest',
    'security.checksum.CRC16'
  );

  /**
   * TestCase for CRC16 digest
   *
   * @see      xp://security.checksum.CRC16Digest
   */
  class CRC16DigestTest extends AbstractDigestTest {
  
    /**
     * Creates a new message digest object
     *
     * @return  security.checksum.MessageDigestImpl
     */
    protected function newDigest() {
      return CRC16::digest();
    }
    
    /**
     * Returns a checksum for a given input string
     *
     * @param   string data
     * @return  security.checksum.Checksum
     */
    protected function checksumOf($data) {
      return CRC16::fromString($data)->getValue();
    }
  }
?>
