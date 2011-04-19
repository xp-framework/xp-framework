<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Encodes/decodes data with MIME base64
   *
   * <code>
   *   $b= Base64::encode($str);
   *   $str= Base64::decode($b);
   * </code>
   *
   * @see      rfc://2045#6.8
   * @purpose  Base 64 encoder/decoder
   */
  class Base64 extends Object {
  
    /**
     * Encode string
     *
     * @param   string str
     * @return  string
     */
    public static function encode($str) { 
      return base64_encode($str);
    }
    
    /**
     * Decode base64 encoded data
     *
     * @param   string str
     * @return  string
     */
    public static function decode($str) { 
      return base64_decode($str);
    }
  }
?>
