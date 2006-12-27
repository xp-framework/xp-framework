<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */

  uses('io.streams.InputStream');

  /**
   * InputStream that reads from a given string.
   *
   * @purpose  InputStream implementation
   */
  class MemoryInputStream extends Object implements InputStream {
    protected
      $pos   = 0,
      $bytes = '';

    /**
     * Constructor
     *
     * @param   string bytes
     */
    public function __construct($bytes) {
      $this->bytes= $bytes;
    }

    /**
     * Read a string
     *
     * @param   int limit default 8192
     * @return  string
     */
    public function read($limit= 8192) {
      $chunk= substr($this->bytes, $this->pos, $limit);
      $this->pos+= strlen($chunk);
      return $chunk;
    }

    /**
     * Returns the number of bytes that can be read from this stream 
     * without blocking.
     *
     */
    public function available() {
      return strlen($this->bytes) - $this->pos;
    }

    /**
     * Close this buffer
     *
     * Note: Closing a memory stream has no effect!
     *
     */
    public function close() {
    }
  }
?>
