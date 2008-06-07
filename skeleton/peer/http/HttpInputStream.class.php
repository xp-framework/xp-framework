<?php
/* This Http is part of the XP framework's experiments
 *
 * $Id$
 */

  uses('io.streams.InputStream', 'peer.http.HttpResponse');

  /**
   * InputStream that reads from a HTTP Response
   *
   * @test     xp://net.xp_framework.unittest.peer.HttpInputStreamTest
   * @purpose  InputStream implementation
   */
  class HttpInputStream extends Object implements InputStream {
    protected
      $response  = NULL,
      $buffer    = '',
      $available = 0;
    
    /**
     * Constructor
     *
     * @param   peer.http.HttpResponse response
     */
    public function __construct(HttpResponse $response) {
      $this->response= $response;
    }
    
    /**
     * Buffer a chunk if necessary
     *
     */
    protected function buffer() {
      if (strlen($this->buffer) > 0) return;
      if (FALSE === ($chunk= $this->response->readData(8192, TRUE))) {
        $this->available= 0;
      } else {
        $this->buffer.= $chunk;
        $this->available= strlen($this->buffer);
      }
    }

    /**
     * Read a string
     *
     * @param   int limit default 8192
     * @return  string
     */
    public function read($limit= 8192) {
      $this->buffer();
      $b= substr($this->buffer, 0, $limit);
      $this->buffer= substr($this->buffer, $limit);
      return $b;
    }

    /**
     * Returns the number of bytes that can be read from this stream 
     * without blocking.
     *
     */
    public function available() {
      $this->buffer();
      return $this->available;
    }

    /**
     * Close this buffer
     *
     */
    public function close() {
      $this->response->closeStream();
    }

    /**
     * Creates a string representation of this Http
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'<'.$this->response->toString().'>';
    }
  }
?>
