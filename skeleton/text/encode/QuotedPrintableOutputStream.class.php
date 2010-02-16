<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('io.streams.OutputStream');

  /**
   * OuputStream that encodes data to QuotedPrintable encoding.
   *
   * Note this does not use the convert.quoted-printable-encode
   * stream filter provided along with PHP as that is buggy.
   *
   * @see      http://en.wikipedia.org/wiki/Quoted-printable
   * @see      rfc://2045 section 6.7
   * @test     xp://net.xp_framework.unittest.text.encode.QuotedPrintableOutputStreamTest
   * @purpose  OuputStream implementation
   */
  class QuotedPrintableOutputStream extends Object implements OutputStream {
    protected $out= NULL;
    protected $l= 0;
    protected $buffer= '';
    
    /**
     * Constructor
     *
     * @param   io.streams.OutputStream out
     */
    public function __construct(OutputStream $out) {
      $this->out= $out;
    }
    
    /**
     * Write a single character. 
     *
     * @param   string c
     * @param   bool encode force encoding
     */
    protected function writeChar($c, $encode= FALSE) {
      if ($encode || '=' === $c || $c < ' ' || $c > '~') {
        $out= sprintf('=%02X', ord($c));
        $this->l+= 3;
      } else {
        $out= $c;
        $this->l+= 1;
      }
      if ($this->l > 75) {
        $this->out->write("=\n");
        $this->l= 0;
      }
      $this->out->write($out);
    }
    
    /**
     * Write a string
     *
     * @param   var arg
     */
    public function write($arg) {
      $arg= $this->buffer.$arg;
      $s= strlen($arg)- 1;
      for ($i= 0; $i < $s; $i++) {
        $this->writeChar($arg{$i});
      }
      $this->buffer= $arg{$s};
    }	

    /**
     * Flush this buffer
     *
     */
    public function flush() {
      $this->writeChar($this->buffer, (' ' === $this->buffer || "\t" === $this->buffer));
      $this->out->flush();
    }

    /**
     * Close this buffer. Flushes this buffer and then calls the close()
     * method on the underlying OuputStream.
     *
     */
    public function close() {
      $this->flush();
      $this->out->close();
    }
  }
?>
