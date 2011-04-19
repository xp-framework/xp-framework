<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
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
  class BufferedInputStreamTest extends TestCase {
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
      $this->mem= new MemoryInputStream(self::BUFFER);
      $this->in= new BufferedInputStream($this->mem, 10);
    }
  
    /**
     * Test reading all
     *
     */
    #[@test]
    public function readAll() {
      $this->assertEquals(new Bytes(self::BUFFER), $this->in->read(strlen(self::BUFFER)));
      $this->assertEquals(0, $this->in->available());
    }

    /**
     * Test reading a five bytes chunk
     *
     */
    #[@test]
    public function readChunk() {
      $this->assertEquals(new Bytes('Hello'), $this->in->read(5));
      $this->assertEquals(5, $this->in->available());   // Five buffered bytes
    }
    
    /**
     * Test reading a five bytes chunk
     *
     */
    #[@test]
    public function readChunks() {
      $this->assertEquals(new Bytes('Hello'), $this->in->read(5));
      $this->assertEquals(5, $this->in->available());   // Five buffered bytes
      $this->assertEquals(new Bytes(' Worl'), $this->in->read(5));
      $this->assertEquals(0, $this->in->available());   // Buffer completely empty
    }

    /**
     * Test closing a stream twice has no effect.
     *
     * @see   xp://lang.Closeable#close
     */
    #[@test]
    public function closingTwice() {
      $this->in->close();
      $this->in->close();
    }
  }
?>
