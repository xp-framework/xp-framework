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
     * Encode string
     *
     * @access  abstract
     * @param   string str
     * @param   string charset default 'iso-8859-1'
     * @return  string
     */
    function encode($str, $charset= 'iso-8859-1') { 
      $r= array(' ' => '_');
      foreach (array_merge(array(61, 46), range(0, 31), range(127, 255)) as $i) {
        $r[chr($i)]= '='.strtoupper(dechex($i));
      }
      return sprintf('=?%s?Q?%s?=', $charset, strtr($str, $r));
    }
    
    /**
     * Decode QuotedPrintable encoded data
     *
     * @access  abstract
     * @param   string str
     * @return  string
     */
    function decode($str) { 
      return quoted_printable_decode($str);
    }
  }
?>
