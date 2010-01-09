<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('io.streams.InputStream', 'io.streams.Streams');

  /**
   * InputStream that decodes from a base64-encoded source 
   *
   * @see      rfc://2045 section 6.8 
   * @test     xp://net.xp_framework.unittest.text.encode.Base64InputStreamTest
   * @purpose  InputStream implementation
   */
  class Base64InputStream extends Object implements InputStream {
    protected $in = NULL;
    
    /**
     * Constructor
     *
     * @param   io.streams.InputStream in
     */
    public function __construct(InputStream $in) {
      $this->in= Streams::readableFd($in);
      if (!stream_filter_append($this->in, 'convert.base64-decode', STREAM_FILTER_READ)) {
        throw new IOException('Could not append stream filter');
      }
    }

    /**
     * Read a string
     *
     * @param   int limit default 8192
     * @return  string
     */
    public function read($limit= 8192) {
      return fread($this->in, $limit);
    }

    /**
     * Returns the number of bytes that can be read from this stream 
     * without blocking.
     *
     */
    public function available() {
      return feof($this->in) ? 0 : 1;
    }

    /**
     * Close this buffer.
     *
     */
    public function close() {
      fclose($this->in);
      $this->in= NULL;
    }
    
    /**
     * Destructor. Ensures output stream is closed.
     *
     */
    public function __destruct() {
      if (!$this->in) return;
      fclose($this->in);
      $this->in= NULL;
    }
  }
?>
