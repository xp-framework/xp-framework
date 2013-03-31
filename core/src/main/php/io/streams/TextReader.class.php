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
    protected $bom = 0;
    protected $charset = NULL;
  
    /**
     * Constructor. Creates a new TextReader on an underlying input
     * stream with a given charset.
     *
     * @param   io.streams.InputStream stream
     * @param   string charset the charset the stream is encoded in or NULL to trigger autodetection by BOM
     */
    public function __construct(InputStream $stream, $charset= NULL) {
      parent::__construct($stream);
      $this->in= Streams::readableFd($stream);
      
      if (NULL === $charset) {
        $charset= $this->detectCharset();
      }
      
      if (!stream_filter_append($this->in, 'convert.iconv.'.$charset.'/'.xp::ENCODING, STREAM_FILTER_READ)) {
        throw new IOException('Could not append stream filter');
      }

      $this->charset= $charset;
    }
    
    /**
     * Returns the character set used
     *
     * @return  string
     */
    public function charset() {
      return $this->charset;
    }
    
    /**
     * Reset to bom. 
     *
     * @throws  io.IOException in case the underlying stream does not support seeking
     */
    public function reset() {
      fseek($this->in, $this->bom, SEEK_SET);
      $this->buf= '';
    }
    
    /**
     * Detect charset of stream
     *
     * @see     http://de.wikipedia.org/wiki/Byte_Order_Mark
     * @see     http://unicode.org/faq/utf_bom.html
     * @return  string
     */
    protected function detectCharset() {
      $c= $this->read(2);
      
      // Check for UTF-16 (BE)
      if ("\376\377" === $c) {
        $this->bom= 2;
        return 'utf-16be';
      }
      
      // Check for UTF-16 (LE)
      if ("\377\376" === $c) {
        $this->bom= 2;
        return 'utf-16le';
      }
      
      // Check for UTF-8 BOM
      if ("\357\273" === $c && "\357\273\277" === ($c.= $this->read(1))) {
        $this->bom= 3;
        return 'utf-8';
      }
      
      // Fall back to ISO-8859-1
      $this->buf= $c;
      $this->bom= 0;
      return 'iso-8859-1';
    }
  
    /**
     * Read a number of characters
     *
     * @param   int size default 8192
     * @return  string NULL when end of data is reached
     */
    public function read($size= 8192) {
      if (0 === $size) return '';

      while (strlen($this->buf) < $size) {
        $c= fread($this->in, $size- strlen($this->buf));
        if ('' === $c) {
          if (xp::errorAt(__FILE__, __LINE__ - 2)) {
            $message= key(xp::$errors[__FILE__][__LINE__ - 3]);
            xp::gc(__FILE__);
            throw new FormatException($message);
          }

          break;
        }

        $this->buf.= $c;
      }

      if ('' === $this->buf) return NULL;

      $chunk= $this->buf;
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
      if (!$this->in) return;
      fclose($this->in);
      $this->in= NULL;
      // No call to parent::close() as fclose() will already close underlying stream
    }

    /**
     * Destructor. Ensures output stream is closed.
     *
     */
    public function __destruct() {
      $this->close();
    }
  }
?>
