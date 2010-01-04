<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('security.checksum.MessageDigest', 'security.checksum.CRC32');

  /**
   * CRC32 digest
   *
   * @test     xp://net.xp_framework.unittest.security.checksum.CRC32DigestTest
   * @see      xp://security.checksum.MessageDigest
   */
  class CRC32Digest extends MessageDigest {

    /**
     * Returns algorithm
     *
     * @return  string
     */
    protected function algorithm() {
      return 'crc32b';
    }

    /**
     * Returns checksum instance
     *
     * @param   string final
     * @return  security.checksum.Checksum
     */
    protected function instance($final) {
      return new CRC32((string)hexdec($final));
    }
  }
?>
