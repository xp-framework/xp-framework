<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.streams.Reader', 'io.streams.Streams');

  /**
   * Reads text from an underlying input stream, converting it from the
   * given character set to our internal encoding (which is iso-8859-1).
   *
   * @test    xp://net.xp_framework.unittest.io.streams.TextReaderTest
   * @ext     iconv
   */
  class TextReader extends Reader {
    protected $in = NULL;
    protected $buf = '';
  
    /**
     * Constructor. Creates a new TextReader on an underlying input
     * stream with a given charset.
     *
     * @param   io.streams.InputStream stream
     * @param   string charset the charset the stream is encoded in.
     */
    public function __construct(InputStream $stream, $charset= 'iso-8859-1') {
      parent::__construct($stream);
      $this->in= Streams::readableFd($stream);
      if (!stream_filter_append($this->in, 'convert.iconv.'.$charset.'/iso-8859-1', STREAM_FILTER_READ)) {
        throw new IOException('Could not append stream filter');
      }
    }
  
    /**
     * Read a number of characters
     *
     * @param   int size default 8192
     * @return  string NULL when end of data is reached
     */
    public function read($size= 8192) {
      if (0 === $size) return '';

      $c= fread($this->in, $size);
      if ('' === $c) {
        if (xp::errorAt(__FILE__, __LINE__ - 2)) {
          $message= key(xp::$registry['errors'][__FILE__][__LINE__ - 3]);
          xp::gc(__FILE__);
          throw new FormatException($message); 
        }
        return NULL;
      }
      
      $chunk= $this->buf.$c;
      $this->buf= '';
      return $chunk;
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

    /**
     * Close this buffer.
     *
     */
    public function close() {
      fclose($this->in);
      $this->in= NULL;
      // No call to parent::close() as fclose() will already close underlying stream
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
