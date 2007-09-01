<?php
/* This class is part of the XP framework
 *
 * $Id: CRC32.class.php 9264 2007-01-12 15:29:23Z kiesel $ 
 */

  namespace security::checksum;
 
  uses('security.checksum.Checksum');
  
  /**
   * CRC32 checksum
   *
   * @see      xp://security.checksum.Checksum
   * @see      php://crc32
   * @purpose  Provide an API to check CRC32 checksums
   */
  class CRC32 extends Checksum {
  
    /**
     * Create a new checksum from a string
     *
     * @param   string str
     * @return  security.checksum.CRC32
     */
    public static function fromString($str) {
      return new (crc32($str));
    }

    /**
     * Create a new checksum from a file object
     *
     * @param   io.File file
     * @return  security.checksum.CRC32
     */
    public static function fromFile($file) {
      try {
        $file->open(FILE_MODE_READ);
        $data= $file->read($file->size());
        $file->close();
      } catch (::Exception $e) {
        throw($e);
      }
      return ::fromString($data);
    }
  }
?>
