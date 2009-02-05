<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */

  uses('io.streams.OutputStream', 'io.File');

  /**
   * OuputStream that writes to files
   *
   * @test     xp://net.xp_framework.unittest.io.streams.FileOutputStreamTest
   * @purpose  OuputStream implementation
   */
  class FileOutputStream extends Object implements OutputStream {
    protected
      $file= NULL;
    
    /**
     * Constructor
     *
     * @param   * file either an io.File object or a string
     * @param   bool append default FALSE whether to append
     */
    public function __construct($file, $append= FALSE) {
      $this->file= $file instanceof File ? $file : new File($file);
      $this->file->open($append ? FILE_MODE_APPEND : FILE_MODE_WRITE);
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
  }
?>
