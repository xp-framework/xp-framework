<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Boolean
   *
   * @model    Static
   * @purpose  Transform string representation of boolean values to bool vice versa
   */
  class WebdavBool extends Object {
  
    /**
     * Return boolean from string
     *
     * @access  private
     * @param   string s
     * @return  bool
     * @throws  IllegalArgumentException
     */
    private function fromString($s) {
      switch ($s) {
        case 't':
        case 'T':
          return TRUE;
          
        case 'f':
        case 'F': 
          return FALSE;
          
        default:  
          throw (new IllegalArgumentException('Value '.$s.' not recognized'));
      }
    }
    
    /**
     * Return string from boolean
     *
     * @access  public
     * @param   bool bool
     * @return  string
     */
    public function fromBool($bool) {
      return $bool ? 'T' : 'F';
    }
  }
?>
