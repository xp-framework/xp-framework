<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents system console
   *
   * <code>
   *   uses('util.cmd.Console');
   *
   *   Console::writeLine('Hello ', 'a', 'b', 1);   // Hello ab1
   *   Console::writeLinef('Hello %s', 'World');    // Hello World
   * </code>
   *
   * @model    static
   * @purpose  I/O functions
   */
  class Console extends Object {

    /**
     * Flush output buffer
     *
     * @model   static
     * @access  public
     */
    function flush() {
      fflush(STDOUT);
    }

    /**
     * Write a string to standard output
     *
     * @model   static
     * @access  public
     * @param   mixed* args
     */
    function write() {
      $a= func_get_args();
      fwrite(STDOUT, implode('', $a));
    }
    
    /**
     * Write a string to standard output and append a newline
     *
     * @model   static
     * @access  public
     * @param   mixed* args
     */
    function writeLine() {
      $a= func_get_args();
      fwrite(STDOUT, implode('', $a)."\n");
    }
    
    /**
     * Write a formatted string to standard output
     *
     * @model   static
     * @access  public
     * @param   string format
     * @param   mixed* args
     * @see     php://printf
     */
    function writef() {
      $a= func_get_args();
      fwrite(STDOUT, vsprintf(array_pop($a), $a));
    }

    /**
     * Write a formatted string to standard output and append a newline
     *
     * @model   static
     * @access  public
     * @param   string format
     * @param   mixed* args
     */
    function writeLinef() {
      $a= func_get_args();
      fwrite(STDOUT, vsprintf(array_shift($a), $a)."\n");
    }
  }
?>
