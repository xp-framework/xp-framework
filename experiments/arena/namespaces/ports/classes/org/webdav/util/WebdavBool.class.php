<?php
/* This class is part of the XP framework
 *
 * $Id: WebdavBool.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace org::webdav::util;

  /**
   * Boolean
   *
   * @purpose  Transform string representation of boolean values to bool vice versa
   */
  class WebdavBool extends lang::Object {
  
    /**
     * Return boolean from string
     *
     * @param   string s
     * @return  bool
     * @throws  lang.IllegalArgumentException
     */
    public static function fromString($s) {
      switch ($s) {
        case 't':
        case 'T':
          return TRUE;
          
        case 'f':
        case 'F': 
        case NULL:
          return FALSE;
          
        default:  
          throw(new lang::IllegalArgumentException('Value '.$s.' not recognized'));
      }
    }
    
    /**
     * Return string from boolean
     *
     * @param   bool bool
     * @return  string
     */
    public static function fromBool($bool) {
      return $bool ? 'T' : 'F';
    }
  }
?>
