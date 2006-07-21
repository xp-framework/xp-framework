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
     * @model   static
     * @access  public
     * @param   string str
     * @return  &security.checksum.CRC32
     */
    public function &fromString($str) {
      return new CRC32(crc32($str));
    }

    /**
     * Create a new checksum from a file object
     *
     * @model   static
     * @access  public
     * @param   &io.File file
     * @return  &security.checksum.CRC32
     */
    public function &fromFile(&$file) {
      try {
        $file->open(FILE_MODE_READ);
        $data= $file->read($file->size());
        $file->close();
      } catch (Exception $e) {
        throw($e);
      }
      return CRC32::fromString($data);
    }
  }
?>
