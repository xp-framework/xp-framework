<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Boolean
   *
   * @purpose  Transform string representation of boolean values to bool vice versa
   */
  class WebdavBool extends Object {
  
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
          throw new IllegalArgumentException('Value '.$s.' not recognized');
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
