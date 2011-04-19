<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.streams.Reader', 'io.streams.Streams');
  
  /**
   * Reads text from an underlying input stream, converting it from the
   * given character set to our internal encoding (which is utf-8).
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
     * @param   string charset the charset the stream is encoded in or NULL to trigger autodetection by BOM
     */
    public function __construct(InputStream $stream, $charset= NULL) {
      parent::__construct($stream);
      $this->in= Streams::readableFd($stream);

      if (NULL === $charset) {
        $charset= $this->detectCharset();
      }

      if (!stream_filter_append($this->in, 'convert.iconv.'.$charset.'/utf-8', STREAM_FILTER_READ | STREAM_FILTER_WRITE)) {
        throw new IOException('Could not append stream filter');
      }
    }

    /**
     * Detect charset of stream
     *
     * @see     http://de.wikipedia.org/wiki/Byte_Order_Mark
     * @see     http://unicode.org/faq/utf_bom.html
     * @return  string
     */
    protected function detectCharset() {
      $c= $this->read0(2);
      
      // Check for UTF-16 (BE)
      if ("\376\377" === $c) {
        return 'utf-16be';
      }
      
      // Check for UTF-16 (LE)
      if ("\377\376" === $c) {
        return 'utf-16le';
      }
      
      // Check for UTF-8 BOM
      if ("\357\273" === $c && "\357\273\277" === ($c.= $this->read0(1))) {
        return 'utf-8';
      }
      
      // Fall back to ISO-8859-1
      $this->buf= iconv('iso-8859-1', 'utf-8', $c);
      return 'iso-8859-1';
    }
    
    /**
     * Read
     *
     * @param   int size default 8192
     * @return  string NULL when end of data is reached
     */
    protected function read0($size= 8192) {
      if (0 === $size) return '';

      while (strlen($this->buf) < $size) {
        $c= fread($this->in, $size- strlen($this->buf));
        if ('' === $c) {
          if (xp::errorAt(__FILE__, __LINE__ - 2)) {
            $message= key(xp::$registry['errors'][__FILE__][__LINE__ - 3]);
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
     * Read a number of characters
     *
     * @param   int size default 8192
     * @return  lang.types.String NULL when end of data is reached
     */
    public function read($size= 8192) {
      if (0 === $size) return new String('');
      if (NULL === ($c= $this->read0(1))) return NULL;
      
      $chunk= '';
      $l= 0;
      do {
        $chunk.= $c;

        // If we have "ü" on the underlying stream, fread(1) returns only the
        // first byte (Ã) of the two-byte-sequence the convert.iconv.- filter 
        // has encoded it to. Read additional characters as defined by utf-8.
        $o= ord($c);
        if ($o >= 0xF0) {
          $chunk.= $this->read0(3);
        } else if ($o >= 0xE0) {
          $chunk.= $this->read0(2);
        } else if ($o >= 0xC0) {
          $chunk.= $this->read0(1);
        }
        $l++;
      } while ($l < $size && NULL !== ($c= $this->read0(1)));
      return new String($chunk, 'utf-8');
    }
    
    /**
     * Read an entire line
     *
     * @return  lang.types.String NULL when end of data is reached
     */
    public function readLine() {
      if (NULL === ($c= $this->read0(1))) return NULL;
      $line= '';
      do {
        if ("\r" === $c) {
          $n= $this->read0(1);
          if ("\n" !== $n) $this->buf= $n.$this->buf;
          return new String($line, 'utf-8');
        } else if ("\n" === $c) {
          return new String($line, 'utf-8');
        }
        $line.= $c;
      } while (NULL !== ($c= $this->read0(1)));
      return new String($line, 'utf-8');
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
