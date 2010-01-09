<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'io.streams.MemoryOutputStream',
    'text.encode.Base64OutputStream'
  );

  /**
   * Test base64 encoder
   *
   * @see   xp://text.encode.Base64OutputStream
   */
  class Base64OutputStreamTest extends TestCase {

    /**
     * Test single write
     *
     */
    #[@test]
    public function singleWrite() {
      $out= new MemoryOutputStream();
      $stream= new Base64OutputStream($out);
      $stream->write('Hello');
      $stream->close();
      $this->assertEquals(base64_encode('Hello'), $out->getBytes());
    }

    /**
     * Test single write
     *
     */
    #[@test]
    public function lineWrappedAt76Characters() {
      $data= str_repeat('1', 75).str_repeat('2', 75);
      $out= new MemoryOutputStream();
      $stream= new Base64OutputStream($out, 76);
      $stream->write($data);
      $stream->close();
      $this->assertEquals(rtrim(chunk_split(base64_encode($data), 76, "\n"), "\n"), $out->getBytes());
    }

    /**
     * Test multiple consecutive writes
     *
     */
    #[@test]
    public function multipeWrites() {
      $out= new MemoryOutputStream();
      $stream= new Base64OutputStream($out);
      $stream->write('Hello');
      $stream->write(' ');
      $stream->write('World');
      $stream->close();
      $this->assertEquals(base64_encode('Hello World'), $out->getBytes());
    }
  }
?>
