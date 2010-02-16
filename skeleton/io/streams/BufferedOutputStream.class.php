<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.streams.OutputStream');

  /**
   * OuputStream that writes to another OutputStream but buffers the
   * results internally. This means not every single byte passed to
   * write() will be written.
   *
   * @purpose  OuputStream implementation
   */
  class BufferedOutputStream extends Object implements OutputStream {
    protected 
      $out  = NULL,
      $buf  = '',
      $size = 0;
    
    /**
     * Constructor
     *
     * @param   io.streams.OutputStream out
     * @param   int size default 512
     */
    public function __construct($out, $size= 512) {
      $this->out= $out;
      $this->size= $size;
    }
    
    /**
     * Write a string
     *
     * @param   var arg
     */
    public function write($arg) { 
      $this->buf.= $arg;
      strlen($this->buf) > $this->size && $this->flush();
    }

    /**
     * Flush this buffer
     *
     */
    public function flush() { 
      $this->out->write($this->buf);
      $this->buf= '';
    }

    /**
     * Close this buffer. Flushes this buffer and then calls the close()
     * method on the underlying OuputStream.
     *
     */
    public function close() {
      $this->flush();
      $this->out->close();
    }
  }
?>
