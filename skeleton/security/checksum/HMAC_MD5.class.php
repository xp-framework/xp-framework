<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses('security.checksum.Checksum');
  
  /**
   * HMAC_MD5 checksum
   *
   * @see      xp://security.checksum.Checksum
   * @purpose  Provide an API to check HMAC_MD5 checksums
   */
  class HMAC_MD5 extends Checksum {
  
    /**
     * Create a new checksum from a string
     *
     * @access  public
     * @param   string str
     * @param   string key default NULL
     * @return  &security.checksum.HMAC_MD5
     */
    function &fromString($str) {
      return new HMAC_MD5(pack('H*', md5($str)));
    }

    /**
     * Create a new checksum from a file object
     *
     * @access  public
     * @param   &io.File file
     * @return  &security.checksum.HMAC_MD5
     */
    function &fromFile(&$file) {
      try(); {
        $file->open(FILE_MODE_READ);
        $data= $file->read($file->size());
        $file->close();
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      return HMAC_MD5::fromString($data);
    }
  }
?>
