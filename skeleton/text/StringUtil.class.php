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
     * of a specified position.
     *
     * @model   static
     * @access  public
     * @param   &string string
     * @param   int pos
     * @param   int len default 1
     */
    function delete(&$string, $pos, $len= 1) {
      $string= substr($string, 0, $pos).substr($string, $pos+ 1);
    }
    
    /**
     * Insert a character into a string at a specified position
     *
     * @access  public
     * @param   &string string
     * @param   înt pos
     * @param   char char
     */
    function insert(&$string, $pos, $char) {
      $string= substr($string, 0, $pos).$char.substr($string, $pos);
    } 
  }
?>
