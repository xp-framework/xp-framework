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
    protected static $invert= FALSE;

    static function __static() {

      // Workaround for http://bugs.php.net/bug.php?id=45028
      // crc32b algorithm's endianess inverted - fixed 2008-08-18
      // and thus with PHP 5.2.7
      self::$invert= '0a1cb779' === hash('crc32b', 'AAAAAAAA');
    }

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
      $crc= hexdec($final);
      if (!self::$invert) {
        return new CRC32($crc);
      } else {
        return new CRC32(sprintf(
          '%u', 
          (($crc & 0xFF) << 24) + (($crc & 0xFF00) << 8) + (($crc & 0xFF0000) >> 8) + (($crc >> 24) & 0xFF)
        ));
      }
    }
  }
?>
