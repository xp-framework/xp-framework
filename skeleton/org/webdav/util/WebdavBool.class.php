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
     * @model   static
     * @access  public
     * @param   string s
     * @return  bool
     * @throws  lang.IllegalArgumentException
     */
    function fromString($s) {
      switch ($s) {
        case 't':
        case 'T':
          return TRUE;
          
        case 'f':
        case 'F': 
        case NULL:
          return FALSE;
          
        default:  
          return throw(new IllegalArgumentException('Value '.$s.' not recognized'));
      }
    }
    
    /**
     * Return string from boolean
     *
     * @model   static
     * @access  public
     * @param   bool bool
     * @return  string
     */
    function fromBool($bool) {
      return $bool ? 'T' : 'F';
    }
  }
?>
