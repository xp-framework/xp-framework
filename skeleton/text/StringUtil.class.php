<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * String utility functions
   *
   * @model    static
   * @purpose  purpose
   */
  class StringUtil extends Object {
  
    /**
     * Delete a specified amount of characters from a string as
     * of a specified position. The resulting string is copied to the 
     * parameter "string" and also returned as result.
     *
     * @model   static
     * @access  public
     * @param   &string string
     * @param   int pos
     * @param   int len default 1
     * @return  string
     */
    function delete(&$string, $pos, $len= 1) {
      $string= substr($string, 0, $pos).substr($string, $pos+ 1);
      return $string;
    }
    
    /**
     * Insert a character into a string at a specified position. The 
     * resulting string is copied to the parameter "string" and also 
     * returned as result.
     *
     * @access  public
     * @param   &string string
     * @param   înt pos
     * @param   char char
     * @return  string
     */
    function insert(&$string, $pos, $char) {
      $string= substr($string, 0, $pos).$char.substr($string, $pos);
      return $string;
    } 
  }
?>
