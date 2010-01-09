<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('io.streams.InputStream');

  /**
   * InputStream that decodes from a QuotedPrintable-encoded source 
   *
   * Note this does not use the convert.quoted-printable-decode
   * stream filter provided along with PHP as that is buggy.
   *
   * @see      http://bugs.php.net/bug.php?id=50363
   * @see      http://bugs.horde.org/ticket/8747
   * @see      rfc://2045 section 6.7
   * @test     xp://net.xp_framework.unittest.text.encode.QuotedPrintableInputStreamTest
   * @purpose  InputStream implementation
   */
  class QuotedPrintableInputStream extends Object implements InputStream {
    protected $in = NULL;
    protected $buffer= '';
    
    /**
     * Constructor
     *
     * @param   io.streams.InputStream in
     */
    public function __construct(InputStream $in) {
      $this->in= $in;
    }

    /**
     * Read a string
     *
     * @param   int limit default 8192
     * @return  string
     */
    public function read($limit= 8192) {
      while ($this->in->available() > 0 && strlen($this->buffer) < $limit) {
        $read= $this->in->read($limit);
        for ($i= 0, $s= strlen($read); $i < $s; $i++) {
          if ('=' === $read{$i}) {
            while ($this->in->available() > 0 && $i > $s- 3) {
              $read.= $this->in->read(3);
              $s= strlen($read);
            }
            if ("\n" === $read{$i+ 1}) {
              $i+= 1;
            } else {
              if (1 !== sscanf($h= substr($read, $i+ 1, 2), '%x', $c)) {
                throw new IOException('Invalid byte sequence "='.$h.'"');
              }
              $this->buffer.= chr($c);
              $i+= 2;
            }
          } else {
            $this->buffer.= $read{$i};
          }
        }
      }
      $chunk= substr($this->buffer, 0, $limit);
      $this->buffer= substr($this->buffer, $limit);
      return $chunk;
    }

    /**
     * Returns the number of bytes that can be read from this stream 
     * without blocking.
     *
     */
    public function available() {
      return $this->in->available();
    }

    /**
     * Close this buffer.
     *
     */
    public function close() {
      $this->in->close();
    }
  }
?>
