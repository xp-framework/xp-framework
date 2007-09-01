<?php
/* This class is part of the XP framework
 *
 * $Id: UTF.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace com::sun;

  /**
   * Encodes/decodes iso-8859-1 to UTF
   *
   * @see      http://java.sun.com/j2se/1.5.0/docs/api/java/io/DataInput.html#readUTF()
   * @see      http://java.sun.com/j2se/1.5.0/docs/api/java/io/DataInput.html#modified-utf-8
   * @purpose  UTF encoder / decoder
   * @experimental
   */
  class UTF extends lang::Object {
  
    /**
     * Encode string
     *
     * @param   string str
     * @return  string
     */
    public static function encode($str) {
      $encoded= utf8_encode($str);
      $return= '';
      do {
        $return.= pack('n', min(strlen($encoded), 0xFFFF)).substr($encoded, 0x0, 0xFFFF);
        $encoded= substr($encoded, 0x10000);
      } while (strlen($encoded) > 0);

      return $return;
    }
    
    /**
     * Decode encoded data
     *
     * @param   string str
     * @return  string
     */
    public static function decode($str) { 
      $return= '';
      do {
        $length= array_pop(unpack('nbytes', substr($str, 0, 2)));
        $return.= substr($str, 2, $length);

        if ($length < 0xFFFF) break;      // Last slice
        $str= substr($str, 2+ $length);
      } while (strlen($str) > 0);
      
      return utf8_decode($return);
    }
  }
?>
