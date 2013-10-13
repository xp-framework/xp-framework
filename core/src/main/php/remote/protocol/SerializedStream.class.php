<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('io.streams.InputStream');

  /**
   * Class that represents a chunk of serialized data in a stream
   *
   * @test     xp://net.xp_framework.unittest.remote.StreamSerializerTest
   */
  class SerializedStream extends Object {
    protected $in;
    protected $buf;
    protected $chunk;

    /**
     * Constructor
     * 
     * @param   io.streams.InputStream in
     * @param   int chunk The chunk size, defaulting to 8192
     */
    public function __construct(InputStream $in, $chunk= 8192) {
      $this->in= $in;
      $this->chunk= $chunk;
      $this->buf= '';
    }

    /**
     * Read a given number of characters, either from the underlying 
     * stream, or from the buffer, if the latter has enough available.
     */
    protected function read($length) {
      $l= strlen($this->buf);
      if ($l < $length) {
        $this->buf.= $this->in->read($length- $l);
      }

      $chunk= substr($this->buf, 0, $length); 
      $this->buf= substr($this->buf, $length);
      return $chunk;
    }

    /**
     * Read until we have either find a delimiter or until we have 
     * consumed the entire content.
     */
    protected function scanFor($delimiters) {
      do {
        $offset= strcspn($this->buf, $delimiters);
        if ($offset < strlen($this->buf)- 1 || !$this->in->available()) break;
        $this->buf.= $this->in->read($this->chunk);
      } while (TRUE);

      $chunk= substr($this->buf, 0, $offset); 
      $this->buf= substr($this->buf, $offset+ 1);   // +1: Skip over delimiter
      return $chunk;
    }

    /**
     * Consume
     *
     * @param   string expected
     * @throws  lang.FormatException in case the expected characters are not found
     */
    public function consume($expected) {
      $chunk= $this->read(strlen($expected));
      if ($expected !== $chunk) {
        throw new FormatException('Expected '.$expected.', have '.$chunk);
      }
    }

    /**
     * Consume a token (x:... where x is the token)
     *
     * @return  string
     */
    public function consumeToken() {
      $chunk= $this->read(2);
      return $chunk{0};
    }

    /**
     * Consume a string ([length]:"[string]")
     * 
     * @return  string
     */
    public function consumeString() {
      $n= $this->scanFor(':');
      $this->read(1);
      $string= $this->read($n);
      $this->read(1 + 1);   // '"' + ';'
      return $string;
    }

    /**
     * Consume everything up to the next ";" character and return it
     * 
     * @return  string
     */     
    public function consumeWord() {
      return $this->scanFor(';');
    }

    /**
     * Consume everything up to the next ":" character and return it
     * 
     * @return  string
     */     
    public function consumeSize() {
      return $this->scanFor(':');
    }
  }
?>
