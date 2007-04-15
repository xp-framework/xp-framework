<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */

  uses('io.streams.InputStream');

  /**
   * InputStream that reads from a file
   *
   * @purpose  InputStream implementation
   */
  class FileInputStream extends Object implements InputStream {
    protected
      $file= NULL;
    
    /**
     * Constructor
     *
     * @param   io.File file
     */
    public function __construct($file) {
      $this->file= $file;
      $this->file->open(FILE_MODE_READ);
    }

    /**
     * Read a string
     *
     * @param   int limit default 8192
     * @return  string
     */
    public function read($limit= 8192) {
      return $this->file->read($limit);
    }

    /**
     * Returns the number of bytes that can be read from this stream 
     * without blocking.
     *
     */
    public function available() {
      return $this->file->size() - $this->file->tell();
    }

    /**
     * Close this buffer
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
