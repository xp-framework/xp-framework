<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
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
      return new CRC32(sprintf('%u', crc32($str)));
    }

    /**
     * Create a new checksum from a file object
     *
     * @param   io.File file
     * @return  security.checksum.CRC32
     */
    public static function fromFile($file) {
      $file->open(FILE_MODE_READ);
      $data= $file->read($file->size());
      $file->close();
      return CRC32::fromString($data);
    }
  }
?>
