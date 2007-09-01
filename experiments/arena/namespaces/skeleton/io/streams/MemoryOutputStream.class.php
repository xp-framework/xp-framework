<?php
/* This file is part of the XP framework's experiments
 *
 * $Id: MemoryOutputStream.class.php 10007 2007-04-15 10:27:53Z kiesel $
 */

  namespace io::streams;

  ::uses('io.streams.OutputStream');

  /**
   * OuputStream that writes to memory
   *
   * @purpose  OuputStream implementation
   */
  class MemoryOutputStream extends lang::Object implements OutputStream {
    protected
      $bytes= '';
    
    /**
     * Write a string
     *
     * @param   mixed arg
     */
    public function write($arg) { 
      $this->bytes.= $arg;
    }

    /**
     * Flush this buffer. A NOOP for this implementation.
     *
     */
    public function flush() { 
    }
    
    /**
     * Retrieve stored bytes
     *
     * @return  string
     */
    public function getBytes() { 
      return $this->bytes;
    }

    /**
     * Close this buffer.
     *
     * Note: Closing a memory stream has no effect!
     *
     */
    public function close() {
    }

    /**
     * Destructor.
     *
     */
    public function __destruct() {
      unset($this->bytes);
    }
  }
?>
