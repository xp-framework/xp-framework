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
     * Destructor. Ensures file is closed.
     *
     */
    protected function __destruct() {
      $this->file->close();
    }
  }
?>
