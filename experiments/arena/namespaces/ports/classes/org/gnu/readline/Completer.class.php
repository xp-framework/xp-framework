<?php
/* This class is part of the XP framework
 *
 * $Id: Completer.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace org::gnu::readline;

  /**
   * ReadLine Completer interface
   *
   * @ext      readline
   * @see      xp://org.gnu.readline.ReadLine#setCompleter
   * @purpose  Interface
   */
  interface Completer {
  
    /**
     * Completion function
     *
     * @param   string str
     * @param   int offset
     * @param   int length
     * @return  string[] completion matches
     */
    public function complete($string, $offset, $length);
  }
  
?>
