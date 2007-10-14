<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */

  uses('io.streams.OutputStream');

  /**
   * OuputStream that writes to files
   *
   * @purpose  OuputStream implementation
   */
  class FileOutputStream extends Object implements OutputStream {
    protected
      $file= NULL;
    
    /**
     * Constructor
     *
     * @param   io.File file
     */
    public function __construct($file) {
      $this->file= $file;
      $this->file->open(FILE_MODE_WRITE);
    }

    /**
     * Write a string
     *
     * @param   mixed arg
     */
    public function write($arg) { 
      $this->file->write($arg);
    }

    /**
     * Flush this buffer. A NOOP for this implementation.
     *
     */
    public function flush() { 
    }

    /**
     * Creates a string representation of this file
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'<'.$this->file->toString().'>';
    }

    /**
     * Close this buffer.
     *
     */
    public function close() {
      $this->file->close();
    }

    /**
     * Destructor. Ensures file is closed.
     *
     */
    public function __destruct() {
      $this->close();
    }
  }
?>
