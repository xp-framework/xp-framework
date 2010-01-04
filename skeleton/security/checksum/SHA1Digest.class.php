<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('security.checksum.MessageDigest', 'security.checksum.SHA1');

  /**
   * SHA1 digest
   *
   * @test     xp://net.xp_framework.unittest.security.checksum.SHA1DigestTest
   * @see      xp://security.checksum.MessageDigest
   */
  class SHA1Digest extends MessageDigest {

    /**
     * Returns algorithm
     *
     * @return  string
     */
    protected function algorithm() {
      return 'sha1';
    }

    /**
     * Returns checksum instance
     *
     * @param   string final
     * @return  security.checksum.Checksum
     */
    protected function instance($final) {
      return new SHA1($final);
    }
  }
?>
