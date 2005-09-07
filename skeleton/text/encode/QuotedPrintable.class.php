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
   * @model    static
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
     * @model   static
     * @access  public
     * @return  int[]
     */
    function getCharsToEncode() {
      static $characters = NULL;
      
      if (!isset($characters)) {
        $characters= array_merge(array(64, 61, 46, 44), range(0, 31), range(127, 255));
      }
      
      return $characters;
    }
  
    /**
     * Encode string
     *
     * @model   static
     * @access  public
     * @param   string str
     * @param   string charset default 'iso-8859-1'
     * @return  string
     */
    function encode($str, $charset= 'iso-8859-1') { 
      $r= array(' ' => '_');
      foreach (QuotedPrintable::getCharsToEncode() as $i) {
        $r[chr($i)]= '='.strtoupper(dechex($i));
      }
      return sprintf('=?%s?Q?%s?=', $charset, strtr($str, $r));
    }
    
    /**
     * Decode QuotedPrintable encoded data
     *
     * @model   static
     * @access  public
     * @param   string str
     * @return  string
     */
    function decode($str) { 
      return strtr(quoted_printable_decode($str), '_', ' ');
    }
  }
?>
