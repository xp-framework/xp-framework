<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$
 */

  uses('OutputStream');

  /**
   * OuputStream that writes to the console
   *
   * Usage:
   * <code>
   *   $out= new ConsoleOutputStream(STDOUT);
   *   $err= new ConsoleOutputStream(STDERR);
   * </code>
   *
   * @purpose  OuputStream implementation
   */
  class ConsoleOutputStream extends Object implements OutputStream {
    protected
      $descriptor= NULL;
    
    /**
     * Constructor
     *
     * @param   resource descriptor one of STDOUT, STDERR
     */
    public function __construct($descriptor) {
      $this->descriptor= $descriptor;
    }

    /**
     * Write a string
     *
     * @param   mixed arg
     */
    public function write($arg) { 
      fwrite($this->descriptor, $arg);
    }
  }
?>
