<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'io.streams.MemoryOutputStream',
    'text.encode.QuotedPrintableOutputStream'
  );

  /**
   * Test QuotedPrintable encoder
   *
   * @see   xp://text.encode.QuotedPrintableOutputStream
   */
  class QuotedPrintableOutputStreamTest extends TestCase {

    /**
     * Test single write
     *
     */
    #[@test]
    public function singleWrite() {
      $out= new MemoryOutputStream();
      $stream= new QuotedPrintableOutputStream($out);
      $stream->write('Hello');
      $stream->close();
      $this->assertEquals('Hello', $out->getBytes());
    }

    /**
     * Test multiple consecutive writes
     *
     */
    #[@test]
    public function multipeWrites() {
      $out= new MemoryOutputStream();
      $stream= new QuotedPrintableOutputStream($out);
      $stream->write('Hello');
      $stream->write(' ');
      $stream->write('World');
      $stream->close();
      $this->assertEquals('Hello World', $out->getBytes());
    }

    /**
     * Test encoding an umlaut
     *
     */
    #[@test]
    public function umlaut() {
      $out= new MemoryOutputStream();
      $stream= new QuotedPrintableOutputStream($out);
      $stream->write('Hello Übercoder');
      $stream->close();
      $this->assertEquals('Hello =DCbercoder', $out->getBytes());
    }

    /**
     * Test encoding an umlaut
     *
     */
    #[@test]
    public function umlautAtTheBeginning() {
      $out= new MemoryOutputStream();
      $stream= new QuotedPrintableOutputStream($out);
      $stream->write('Übercoder');
      $stream->close();
      $this->assertEquals('=DCbercoder', $out->getBytes());
    }

    /**
     * Test encoding lines 150 bytes of data should end up in two lines.
     *
     */
    #[@test]
    public function lineLengthMayNotBeLongerThan76Characters() {
      $out= new MemoryOutputStream();
      $stream= new QuotedPrintableOutputStream($out);
      $stream->write(str_repeat('1', 75));
      $stream->write(str_repeat('2', 75));
      $stream->close();
      $this->assertEquals(str_repeat('1', 75)."=\n".str_repeat('2', 75), $out->getBytes());
    }

    /**
     * Test end of data
     *
     */
    #[@test]
    public function spaceAtEndOfMustBeEncoded() {
      $out= new MemoryOutputStream();
      $stream= new QuotedPrintableOutputStream($out);
      $stream->write('Hello ');
      $stream->close();
      $this->assertEquals('Hello=20', $out->getBytes());
    }

    /**
     * Test decoding an equals sign
     *
     */
    #[@test]
    public function equalsSign() {
      $out= new MemoryOutputStream();
      $stream= new QuotedPrintableOutputStream($out);
      $stream->write('A=1');
      $stream->close();
      $this->assertEquals('A=3D1', $out->getBytes());
    }
  }
?>
