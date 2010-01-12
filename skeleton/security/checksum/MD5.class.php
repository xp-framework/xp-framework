<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses('security.checksum.Checksum');
  
  /**
   * MD5 checksum
   *
   * @see      xp://security.checksum.Checksum
   * @see      php://md5
   * @purpose  Provide an API to check MD5 checksums
   */
  class MD5 extends Checksum {
  
    /**
     * Create a new checksum from a string
     *
     * @param   string str
     * @return  security.checksum.MD5
     */
    public static function fromString($str) {
      return new MD5(md5($str));
    }

    /**
     * Returns message digest
     *
     * @return  security.checksum.MessageDigestImpl
     */
    public static function digest() {
      return MessageDigest::newInstance('md5');
    }

    /**
     * Create a new checksum from a file object
     *
     * @param   io.File file
     * @return  security.checksum.MD5
     */
    public static function fromFile($file) {
      return new MD5(md5_file($file->uri));
    }
  }
?>
