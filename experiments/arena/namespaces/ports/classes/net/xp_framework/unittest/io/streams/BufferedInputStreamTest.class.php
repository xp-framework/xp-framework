<?php
/* This class is part of the XP framework
 *
 * $Id: BufferedInputStreamTest.class.php 8963 2006-12-27 14:21:05Z friebe $ 
 */

  namespace net::xp_framework::unittest::io::streams;

  ::uses(
    'unittest.TestCase',
    'io.streams.BufferedInputStream',
    'io.streams.MemoryInputStream'
  );

  /**
   * Unit tests for streams API
   *
   * @see      xp://io.streams.InputStream
   * @purpose  Unit test
   */
  class BufferedInputStreamTest extends unittest::TestCase {
    const BUFFER= 'Hello World, how are you doing?';

    protected 
      $in = NULL,
      $mem= NULL;
    
    /**
     * Setup method. Creates the fixture, a BufferedInputStream with
     * a buffer size of 10 characters.
     *
     */
    public function setUp() {
      $this->mem= new io::streams::MemoryInputStream(self::BUFFER);
      $this->in= new io::streams::BufferedInputStream($this->mem, 10);
    }
  
    /**
     * Test reading all
     *
     */
    #[@test]
    public function readAll() {
      $this->assertEquals(self::BUFFER, $this->in->read(strlen(self::BUFFER)));
      $this->assertEquals(0, $this->in->available());
    }

    /**
     * Test reading a five bytes chunk
     *
     */
    #[@test]
    public function readChunk() {
      $this->assertEquals('Hello', $this->in->read(5));
      $this->assertEquals(5, $this->in->available());   // Five buffered bytes
    }
    
    /**
     * Test reading a five bytes chunk
     *
     */
    #[@test]
    public function readChunks() {
      $this->assertEquals('Hello', $this->in->read(5));
      $this->assertEquals(5, $this->in->available());   // Five buffered bytes
      $this->assertEquals(' Worl', $this->in->read(5));
      $this->assertEquals(0, $this->in->available());   // Buffer completely empty
    }
  }
?>
