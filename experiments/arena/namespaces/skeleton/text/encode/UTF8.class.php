<?php
/* This class is part of the XP framework
 *
 * $Id: UTF8.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace text::encode;

  /**
   * Encodes/decodes iso-8859-1 to utf-8
   *
   * <code>
   *   $b= UTF8::encode($str);
   *   $str= UTF8::decode($b);
   * </code>
   *
   * @see      rfc://2045#6.8
   * @purpose  UTF encoder / decoder
   */
  class UTF8 extends lang::Object {
  
    /**
     * Encode string
     *
     * @param   string str
     * @return  string
     */
    public static function encode($str) { 
      return utf8_encode($str);
    }
    
    /**
     * Decode utf8 encoded data
     *
     * @param   string str
     * @return  string
     */
    public static function decode($str) { 
      return utf8_decode($str);
    }
  }
?>
