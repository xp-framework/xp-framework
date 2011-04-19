<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */

  uses('io.streams.OutputStreamWriter');

  /**
   * A OutputStreamWriter implementation that writes the string values of
   * the given arguments to the underlying output stream.
   *
   * @test     xp://net.xp_framework.unittest.io.streams.StringWriterTest
   * @purpose  OutputStreamWriter implementation
   */
  class StringWriter extends Object implements OutputStreamWriter {
    protected
      $out= NULL;
    
    /**
     * Constructor
     *
     * @param   io.streams.OutputStream out
     */
    public function __construct($out) {
      $this->out= $out;
    }
    
    /**
     * Return underlying output stream
     *
     * @return  io.streams.OutputStream
     */
    public function getStream() {
      return $this->out;
    }

    /**
     * Return underlying output stream
     *
     * @param   io.streams.OutputStream stream
     */
    public function setStream(OutputStream $stream) {
      $this->out= $stream;
    }

    /**
     * Creates a string representation of this writer
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName()."@{\n  ".$this->out->toString()."\n}";
    }
  
    /**
     * Flush output buffer
     *
     */
    public function flush() {
      $this->out->flush();
    }

    /**
     * Print arguments
     *
     * @param   var* args
     */
    public function write() {
      $a= func_get_args();
      foreach ($a as $arg) {
        if ($arg instanceof Generic) {
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
     * @param   var* args
     */
    public function writeLine() {
      $a= func_get_args();
      foreach ($a as $arg) {
        if ($arg instanceof Generic) {
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
     * Print a formatted string
     *
     * @param   string format
     * @param   var* args
     * @see     php://writef
     */
    public function writef() {
      $a= func_get_args();
      $this->out->write(vsprintf(array_shift($a), $a));
    }

    /**
     * Print a formatted string and append a newline
     *
     * @param   string format
     * @param   var* args
     */
    public function writeLinef() {
      $a= func_get_args();
      $this->out->write(vsprintf(array_shift($a), $a)."\n");
    }
  }
?>
