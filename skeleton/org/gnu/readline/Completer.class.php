<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * ReadLine Completer interface
   *
   * @ext      readline
   * @see      xp://org.gnu.readline.ReadLine#setCompleter
   * @purpose  Interface
   */
  class Completer extends Interface {
  
    /**
     * Completion function
     *
     * @access  public
     * @param   string str
     * @param   int offset
     * @param   int length
     * @return  string[] completion matches
     */
    function complete($string, $offset, $length) { }
  }
  
?>
