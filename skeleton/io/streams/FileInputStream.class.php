<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */

  uses('io.streams.InputStream', 'io.streams.Seekable');

  /**
   * InputStream that reads from a file
   *
   * @purpose  InputStream implementation
   */
  class FileInputStream extends Object implements InputStream, Seekable {
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
      $this->file->isOpen() && $this->close();
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
     * Seek to a given offset
     *
     * @param   int offset
     * @param   int whence default SEEK_SET (one of SEEK_[SET|CUR|END])
     * @throws  io.IOException in case of error
     */
    public function seek($offset, $whence= SEEK_SET) {
      $this->file->seek($offset, $whence);
    }

    /**
     * Return current offset
     *
     * @return  int offset
     */
    public function tell() {
      return $this->file->tell();
    }
  }
?>
