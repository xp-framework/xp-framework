<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */

  uses('io.streams.InputStreamReader');

  /**
   * A InputStreamReader implementation that reads string values of
   * the given arguments from the underlying input stream.
   *
   * @test     xp://net.xp_framework.unittest.io.streams.StringReaderTest
   * @purpose  InputStreamReader implementation
   */
  class StringReader extends Object implements InputStreamReader {
    protected
      $in  = NULL,
      $buf = '';
    
    /**
     * Constructor
     *
     * @param   io.streams.InputStream in
     */
    public function __construct($in) {
      $this->in= $in;
    }
    
    /**
     * Return underlying input stream
     *
     * @return  io.streams.InputStream
     */
    public function getStream() {
      return $this->in;
    }

    /**
     * Set underlying input stream
     *
     * @param   io.streams.InputStream stream
     */
    public function setStream(InputStream $stream) {
      $this->in= $stream;
      $this->buf= '';
    }

    /**
     * Creates a string representation of this writer
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName()."@{\n  ".$this->in->toString()."\n}";
    }

    /**
     * Read a number of bytes
     *
     * @param   int size default 8192
     * @return  string
     */
    public function read($size= 8192) {
      while (strlen($this->buf) < $size && $this->in->available()) {
        if (NULL === ($read= $this->in->read(512))) break;
        $this->buf.= $read;
      }
      $chunk= substr($this->buf, 0, $size);
      $this->buf= substr($this->buf, $size);
      return $chunk;
    }
    
    /**
     * Read an entire line
     *
     * @return  string
     */
    public function readLine() {
      $line= '';
      do {
        $c= $this->read(1);
        if ("\r" === $c) {
          $n= $this->read(1);
          if ("\n" !== $n) $this->buf= $n.$this->buf;
          return $line;
        } else if ("\n" === $c) {
          return $line;
        }
        $line.= $c;
      } while ($c !== FALSE);
      return $line;
    }
  }
?>
