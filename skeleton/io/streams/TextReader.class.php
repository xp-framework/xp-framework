<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.streams.Reader');

  /**
   * Reads text from an underlying input stream.
   *
   * @test    xp://net.xp_framework.unittest.io.streams.TextReaderTest
   * @ext     iconv
   */
  class TextReader extends Reader {
    protected $charset= '';
    protected $buf= '';
  
    /**
     * Constructor. Creates a new TextReader on an underlying input
     * stream with a given charset.
     *
     * @param   io.streams.InputStream stream
     * @param   string charset the charset the stream is encoded in.
     */
    public function __construct(InputStream $stream, $charset= 'iso-8859-1') {
      parent::__construct($stream);
      $this->charset= $charset;
    }
  
    /**
     * Read a number of characters
     *
     * @param   int size default 8192
     * @return  string NULL when end of data is reached
     */
    public function read($size= 8192) {
      if (0 === $size) return '';       // Short-circuit this
      while (@iconv_strlen($this->buf, $this->charset) < $size && $this->stream->available()) {
        if (NULL === ($read= $this->stream->read(512))) break;
        $this->buf.= $read;
      }
      $chunk= iconv_substr($this->buf, 0, $size, $this->charset);
      $this->buf= substr($this->buf, strlen($chunk));
      return FALSE === $chunk ? NULL : iconv($this->charset, 'iso-8859-1', $chunk);
    }
    
    /**
     * Read an entire line
     *
     * @return  string NULL when end of data is reached
     */
    public function readLine() {
      if (NULL === ($c= $this->read(1))) return NULL;
      $line= '';
      do {
        if ("\r" === $c) {
          $n= $this->read(1);
          if ("\n" !== $n) $this->buf= $n.$this->buf;
          return $line;
        } else if ("\n" === $c) {
          return $line;
        }
        $line.= $c;
      } while (NULL !== ($c= $this->read(1)));
      return $line;
    }
  }
?>
