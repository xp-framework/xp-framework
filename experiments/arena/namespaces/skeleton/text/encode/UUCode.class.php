<?php
/* This class is part of the XP framework
 *
 * $Id: UUCode.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace text::encode;

  /**
   * Encodes/decodes uuencode 
   *
   * Usage example (encoding a file):
   * <code>
   *   uses('io.File', 'io.FileUtil', 'text.encode.UUCode');
   * 
   *   $encoded= UUCode::encode(FileUtil::getContents(new File($argv[1])));  
   *   printf(
   *     "begin 644 %s\n%s\nend\n",
   *     $argv[1],
   *     $encoded
   *   );
   * </code>
   *
   * @see      http://foldoc.hld.c64.org/foldoc.cgi?uuencode
   * @see      http://www.opengroup.org/onlinepubs/007908799/xcu/uuencode.html 
   * @purpose  UUEncode encoder / decoder
   */
  class UUCode extends lang::Object {

    /**
     * Encode string
     *
     * @param   string str
     * @return  string
     */
    public static function encode($str) {
      $out= '';
      $offset= 0;
      while ($chunk= substr($str, $offset, 0x2D)) {
        $out.= chr((strlen($chunk) & 0x3F) + 0x20);
        for ($i= 0, $s= strlen($chunk); $i < $s; $i+= 3) {
          $out.= strtr( 
            chr(((ord($chunk{$i}) >> 2) & 0x3F) + 0x20).
            chr(((((ord($chunk{$i}) << 4) & 0x30) | ((ord($chunk{$i+ 1}) >> 4) & 0x0F)) & 0x3F) + 0x20).
            chr(((((ord($chunk{$i+ 1}) << 2) & 0x3C) | ((ord($chunk{$i+ 2}) >> 6) & 0x03)) & 0x3F) + 0x20).
            chr(((ord($chunk{$i+ 2}) & 0x3F) & 0x3F) + 0x20),
            ' ', '`'
          );
        }
        $out.= "\n";
        $offset+= 0x2D;
      }
      return $out.'`';
    }    
    
    /**
     * Decode uuencoded data
     *
     * @param   string str
     * @return  string
     */
    public static function decode($str) {
      $chunk= strtok($str, "\n");
      $out= '';
      do {
        if ('`' == $chunk{0}) break;
        for ($i= 1, $s= strlen($chunk); $i < $s; $i+= 4) {
          $out.= (
            chr((((ord($chunk{$i}) - 0x20) & 0x3F) << 2) | (((ord($chunk{$i+ 1}) - 0x20) & 0x3F) >> 4)).
            chr((((ord($chunk{$i+ 1}) - 0x20) & 0x3F) << 4) | (((ord($chunk{$i+ 2}) - 0x20) & 0x3F) >> 2)).
            chr((((ord($chunk{$i+ 2}) - 0x20) & 0x3F) << 6) | ((ord($chunk{$i+ 3}) - 0x20) & 0x3F))
          );
        }
      } while ($chunk= strtok("\n"));

      return rtrim($out, "\0");
    }
  }
?>
