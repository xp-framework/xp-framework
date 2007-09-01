<?php
/* This class is part of the XP framework's experiments
 *
 * $Id: ConsoleOutputStream.class.php 10066 2007-04-20 13:35:23Z friebe $
 */

  namespace io::streams;

  ::uses('io.streams.OutputStream');

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
  class ConsoleOutputStream extends lang::Object implements OutputStream {
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
     * Creates a string representation of this output stream
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'<'.$this->descriptor.'>';
    }

    /**
     * Write a string
     *
     * @param   mixed arg
     */
    public function write($arg) { 
      fwrite($this->descriptor, $arg);
    }

    /**
     * Flush this buffer.
     *
     */
    public function flush() { 
      fflush($this->descriptor);
    }

    /**
     * Close this buffer.
     *
     */
    public function close() {
      fclose($this->descriptor);
    }
  }
?>
