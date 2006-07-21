<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Encodes/decodes iso-8859-1 to utf-7
   *
   * <code>
   *   $b= UTF7::encode($str);
   *   $str= UTF7::decode($b);
   * </code>
   *
   * @ext      imap
   * @see      rfc://2060
   * @see      rfc://1642
   * @purpose  UTF encoder / decoder
   */
  class UTF7 extends Object {
  
    /**
     * Encode string
     *
     * @model   static
     * @access  public
     * @param   string str
     * @return  string
     */
    public function encode($str) { 
      return imap_utf7_encode($str);
    }
    
    /**
     * Decode utf7 encoded data
     *
     * @model   static
     * @access  public
     * @param   string str
     * @return  string
     */
    public function decode($str) { 
      return imap_utf7_decode($str);
    }
  }
?>
