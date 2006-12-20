<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */

  /**
   * A PrintStream adds functionality to another output stream, namely 
   * the ability to write representations of various data values 
   * conveniently.
   *
   * @purpose  Stream implementation
   */
  class PrintStream extends Object {
    var
      $out= NULL;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   &OutputStream out
     */
    function __construct(&$out) {
      $this->out= deref($out);
    }
  
    /**
     * Flush output buffer
     *
     * @access  public
     */
    function flush() {
      $this->out->flush();
    }

    /**
     * Print arguments
     *
     * @access  public
     * @param   mixed* args
     */
    function write() {
      $a= func_get_args();
      foreach ($a as $arg) {
        if (is_a($arg, 'Object')) {
          $this->out->write($arg->toString());
        } else if (is_array($arg)) {
          $this->out->write(xp::stringOf($arg));
        } else {
          $this->out->write($arg);
        }
      }
    }
    
    /**
     * Print arguments and append a newline
     *
     * @access  public
     * @param   mixed* args
     */
    function writeLine() {
      $a= func_get_args();
      foreach ($a as $arg) {
        if (is_a($arg, 'Object')) {
          $this->out->write($arg->toString());
        } else if (is_array($arg)) {
          $this->out->write(xp::stringOf($arg));
        } else {
          $this->out->write($arg);
        }
      }
      $this->out->write("\n");
    }
    
    /**
     * Print a formatted string to standard output
     *
     * @access  public
     * @param   string format
     * @param   mixed* args
     * @see     php://writef
     */
    function writef() {
      $a= func_get_args();
      $this->out->write(vswritef(array_shift($a), $a));
    }

    /**
     * Print a formatted string and append a newline
     *
     * @access  public
     * @param   string format
     * @param   mixed* args
     */
    function writeLinef() {
      $a= func_get_args();
      $this->out->write(vswritef(array_shift($a), $a)."\n");
    }
  }
?>
