<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses('security.checksum.Checksum');
  
  /**
   * CRC32 checksum
   *
   * @test     xp://net.xp_framework.unittest.security.checksum.CRC32Test
   * @see      xp://security.checksum.Checksum
   * @see      php://crc32
   * @purpose  Provide an API to check CRC32 checksums
   */
  class CRC32 extends Checksum {

    /**
     * Constructor
     *
     * @param   mixed value
     */
    public function __construct($value) {
      if (is_int($value)) {
        parent::__construct(sprintf('%08x', $value)); 
      } else {
        parent::__construct($value);
      }
    }
  
    /**
     * Create a new checksum from a string
     *
     * @param   string str
     * @return  security.checksum.CRC32
     */
    public static function fromString($str) {
      return new CRC32(crc32($str));
    }
    
    /**
     * Returns message digest
     *
     * @return  security.checksum.MessageDigestImpl
     */
    public static function digest() {
      return MessageDigest::newInstance('crc32b');
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
    
    /**
     * Returns a signed int32 of this checksum's value for use
     * with pack('V')
     *
     * @see     php://pack
     * @return  int
     */
    public function asInt32() {
      $v= hexdec($this->value);
      if ($v > 2147483647) {      // Convert from uint32 to int32
        return intval($v - 4294967296);
      }
      return $v;
    }
  }
?>
