<?php
/* This file is part of the XP framework
 *
 * $Id$
 */

  uses('io.streams.InputStream', 'io.archive.zip.Compression');

  /**
   * Zip File input stream. Reads from the current position up until a
   * certain length.
   *
   * @purpose  InputStream implementation
   */
  class ZipFileInputStream extends Object implements InputStream {
    protected 
      $reader      = NULL,
      $pos         = 0,
      $length      = 0;

    /**
     * Constructor
     *
     * @param   io.archive.zip.AbstractZipReaderImpl reader
     * @param   int length
     */
    public function __construct(AbstractZipReaderImpl $reader, $length) {
      $this->reader= $reader;
      $this->length= $length;
    }

    /**
     * Read a string
     *
     * @param   int limit default 8192
     * @return  string
     */
    public function read($limit= 8192) {
      if ($this->pos >= $this->length) {
        throw new IOException('EOF');
      }
      $chunk= $this->reader->streamRead(min($limit, $this->length- $this->pos));
      $l= strlen($chunk);
      $this->pos+= $l;
      $this->reader->skip-= $l;
      return $chunk;
    }

    /**
     * Returns the number of bytes that can be read from this stream 
     * without blocking.
     *
     */
    public function available() {
      return $this->pos < $this->length ? $this->reader->streamAvailable() : 0;
    }

    /**
     * Close this buffer
     *
     */
    public function close() {
      // NOOP, leave underlying stream open
    }
  }
?>
