<?php
/* This file is part of the XP framework's experiments
 *
 * $Id: FileOutputStream.class.php 10007 2007-04-15 10:27:53Z kiesel $
 */

  namespace io::streams;

  ::uses('io.streams.OutputStream');

  /**
   * OuputStream that writes to files
   *
   * @purpose  OuputStream implementation
   */
  class FileOutputStream extends lang::Object implements OutputStream {
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
