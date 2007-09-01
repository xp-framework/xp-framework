<?php
/* This class is part of the XP framework
 *
 * $Id: MD5.class.php 9264 2007-01-12 15:29:23Z kiesel $ 
 */

  namespace security::checksum;
 
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
      return new (md5($str));
    }

    /**
     * Create a new checksum from a file object
     *
     * @param   io.File file
     * @return  security.checksum.MD5
     */
    public static function fromFile($file) {
      return new (md5_file($file->uri));
    }
  }
?>
