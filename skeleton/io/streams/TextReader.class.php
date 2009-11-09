<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.streams.Reader');

  /**
   * Reads text from an underlying input stream, converting it from the
   * given character set to our internal encoding (which is iso-8859-1).
   *
   * @test    xp://net.xp_framework.unittest.io.streams.TextReaderTest
   * @ext     iconv
   */
  class TextReader extends Reader {
    protected $charset= '';
    protected $buf= '';
    protected $bl= 0;
  
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
      
      // See if we have enough characters in the buffer. If we
      // don't, fill up the buffer until we do.
      $buffered= $this->bl;
      while ($buffered < $size && $this->stream->available()) {
      
        // Read a chunk (the difference in buffered length and requested 
        // size, but a minimum of 128 bytes) and see if we catch 
        // a situation like this
        //
        //   "He is the Übercoder" (UTF-8)
        //    ^^^^^^^^^^^
        //    chunk
        //
        // In this case, we need to read more bytes until we have enough 
        // bytes to form a complete character. If we reach the end of the
        // underlying stream we can be sure the input is malformed.
        $read= $this->stream->read(max($size - $this->bl, 0x80));
        while (FALSE === ($len= @iconv_strlen($read, $this->charset))) {
          if (!$this->stream->available()) {
            throw new FormatException('Broken input string "'.$read.'"');
          }
          $read.= $this->stream->read(1);
        }
        
        // The read variable now contains valid characters in the relevant
        // character set, so we can concatenate it to the buffer
        $buffered+= $len;
        $this->buf.= $read;
      }
      
      // Finally: We might have exceeded the size given, so only return 
      // exactly the amount of requested characters.
      if (0 === $buffered) {
        return NULL;
      } else if (1 === $buffered) {
        $chunk= $this->buf;
        $this->buf= '';
        $this->bl= 0;
      } else {
        $chunk= iconv_substr($this->buf, 0, $size, $this->charset);
        $this->buf= substr($this->buf, strlen($chunk));
        $this->bl= iconv_strlen($this->buf, $this->charset);
      }
      
      // Ignore characters not convertible to iso-8859-1
      return @iconv($this->charset, 'iso-8859-1//IGNORE', $chunk);
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
