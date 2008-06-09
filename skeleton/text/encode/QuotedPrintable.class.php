<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Encodes/decodes for quoted printable data
   *
   * <code>
   *   $b= QuotedPrintable::encode($str);
   *   $str= QuotedPrintable::decode($b);
   * </code>
   *
   * @see      rfc://2045#6.7
   * @purpose  Quoted Printable encoder / decoder
   */
  class QuotedPrintable extends Object {
  
    /**
     * Get ASCII values of characters that need to be encoded
     *
     * Note: According to RFC 2045, the "@" need not be escaped
     * Exim has its problems though if an "@" sign appears in an 
     * name (even if it's encoded), such as:
     *
     * <pre>
     *   =?iso-8859-1?Q?Timm@Home?= <timm@example.com>
     * </pre>
     *
     * This is why "64" is added to the first array in this function.
     *
     * Note: The colon (":") needs to encoded because colons are of
     * special meaning to Exim.
     *
     * This is why "58" is added to the first array in this function.
     *
     * @return  int[]
     */
    public static function getCharsToEncode() {
      static $characters = NULL;
      
      if (!isset($characters)) {
        $characters= array_merge(array(64, 58, 61, 46, 44, 95), range(0, 31), range(127, 255));
      }
      
      return $characters;
    }
  
    /**
     * Encode string
     *
     * @param   string str
     * @param   string charset default 'iso-8859-1'
     * @return  string
     */
    public static function encode($str, $charset= 'iso-8859-1') { 
      $r= array(' ' => '_');
      foreach (QuotedPrintable::getCharsToEncode() as $i) {
        $r[chr($i)]= '='.strtoupper(dechex($i));
      }
      return sprintf('=?%s?Q?%s?=', $charset, strtr($str, $r));
    }
    
    /**
     * Decode QuotedPrintable encoded data
     *
     * @param   string str
     * @return  string
     */
    public static function decode($str) { 
      return strtr(quoted_printable_decode($str), '_', ' ');
    }
  }
?>
