<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'io.streams.MemoryInputStream',
    'text.encode.Base64InputStream'
  );

  /**
   * Test base64 decoder
   *
   * @see   xp://text.encode.Base64InputStream
   */
  class Base64InputStreamTest extends TestCase {

    /**
     * Test single read
     *
     */
    #[@test]
    public function singleRead() {
      $stream= new Base64InputStream(new MemoryInputStream(base64_encode('Hello')));
      $chunk= $stream->read();
      $stream->close();
      $this->assertEquals('Hello', $chunk);
    }

    /**
     * Test multiple consecutive reads
     *
     */
    #[@test]
    public function multipleReads() {
      $stream= new Base64InputStream(new MemoryInputStream(base64_encode('Hello World')));
      $chunk1= $stream->read(5);
      $chunk2= $stream->read(1);
      $chunk3= $stream->read(5);
      $stream->close();
      $this->assertEquals('Hello', $chunk1);
      $this->assertEquals(' ', $chunk2);
      $this->assertEquals('World', $chunk3);
    }
  }
?>
