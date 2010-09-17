<?php
/* This file is part of the XP framework
 *
 * $Id$
 */

  uses('io.streams.InputStream');

  /**
   * Buffered InputStream
   *
   * @purpose  InputStream implementation
   */
  class BufferedInputStream extends Object implements InputStream {
    protected 
      $in   = NULL,
      $buf  = '',
      $size = 0;

    /**
     * Constructor
     *
     * @param   io.streams.InputStream in
     * @param   int size default 512
     */
    public function __construct($in, $size= 512) {
      $this->in= $in;
      $this->size= $size;
    }

    /**
     * Read a string
     *
     * @param   int limit default 8192
     * @return  lang.types.Bytes
     */
    public function read($limit= 8192) {
      while (strlen($this->buf) < $limit) {
        if (NULL === ($read= $this->in->read($this->size))) break;
        $this->buf.= $read;
      }
      $chunk= substr($this->buf, 0, $limit);
      $this->buf= substr($this->buf, $limit);
      return new Bytes($chunk);
    }

    /**
     * Returns the number of bytes that can be read from this stream 
     * without blocking.
     *
     */
    public function available() {
      return strlen($this->buf);
    }

    /**
     * Close this buffer
     *
     */
    public function close() {
    }
  }
?>
