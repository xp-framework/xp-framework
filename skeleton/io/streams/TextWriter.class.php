<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('io.streams.Writer');

  /**
   * Writes text from to underlying output stream. When writing lines,
   * uses the newLine property's bytes as newline separator, defaulting
   * to "\n".
   *
   * @test    xp://net.xp_framework.unittest.io.streams.TextWriterTest
   * @ext     iconv
   */
  class TextWriter extends Writer {
    protected $charset= '';
    protected $newLine= "\n";
  
    /**
     * Constructor. Creates a new TextWriter on an underlying output
     * stream with a given charset.
     *
     * @param   io.streams.OutputStream stream
     * @param   string charset the charset the stream is encoded in.
     */
    public function __construct(OutputStream $stream, $charset= 'iso-8859-1') {
      parent::__construct($stream);
      $this->charset= $charset;
    }

    /**
     * Sets newLine property's bytes
     *
     * @param   string newLine
     */
    public function setNewLine($newLine) {
      $this->newLine= $newLine;
    }
    
    /**
     * Sets newLine property's bytes and returns this writer
     *
     * @param   string newLine
     * @return  io.streams.TextWriter this
     */
    public function withNewLine($newLine) {
      $this->newLine= $newLine;
      return $this;
    }

    /**
     * Gets newLine property's bytes
     *
     * @return  string newLine
     */
    public function getNewLine() {
      return $this->newLine;
    }
  
    /**
     * Write characters
     *
     * @param   string text
     */
    public function write($text) {
      $this->stream->write(iconv('iso-8859-1', $this->charset, $text));
    }
    
    /**
     * Write an entire line
     *
     * @param   string text
     */
    public function writeLine($text= '') {
      $this->stream->write(iconv('iso-8859-1', $this->charset, $text).$this->newLine);
    }
  }
?>
