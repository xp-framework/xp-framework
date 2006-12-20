<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$
 */

  /**
   * OuputStream that writes to the console
   *
   * Usage:
   * <code>
   *   $out= &new ConsoleOutputStream(STDOUT);
   *   $err= &new ConsoleOutputStream(STDERR);
   * </code>
   *
   * @purpose  OuputStream implementation
   */
  class ConsoleOutputStream extends Object {
    var
      $descriptor= NULL;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   resource descriptor one of STDOUT, STDERR
     */
    function __construct($descriptor) {
      $this->descriptor= $descriptor;
    }

    /**
     * Write a string
     *
     * @access  public
     * @param   mixed arg
     */
    function write($arg) { 
      fwrite($this->descriptor, $arg);
    }

  } implements(__FILE__, 'OutputStream');
?>
