<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('security.checksum.MessageDigest', 'security.checksum.MD5');

  /**
   * MD5 digest
   *
   * @test     xp://net.xp_framework.unittest.security.checksum.MD5DigestTest
   * @see      xp://security.checksum.MessageDigest
   */
  class MD5Digest extends MessageDigest {

    /**
     * Returns algorithm
     *
     * @return  string
     */
    protected function algorithm() {
      return 'md5';
    }

    /**
     * Returns checksum instance
     *
     * @param   string final
     * @return  security.checksum.Checksum
     */
    protected function instance($final) {
      return new MD5($final);
    }
  }
?>
