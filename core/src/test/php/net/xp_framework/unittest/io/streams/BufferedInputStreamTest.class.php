<?php namespace net\xp_framework\unittest\io\streams;

use unittest\TestCase;
use io\streams\BufferedInputStream;
use io\streams\MemoryInputStream;

/**
 * Unit tests for streams API
 *
 * @see   xp://io.streams.InputStream
 * @see   xp://lang.Closeable#close
 */
class BufferedInputStreamTest extends TestCase {
  const BUFFER= 'Hello World, how are you doing?';

  protected 
    $in = null,
    $mem= null;
  
  /**
   * Setup method. Creates the fixture, a BufferedInputStream with
   * a buffer size of 10 characters.
   */
  public function setUp() {
    $this->mem= new MemoryInputStream(self::BUFFER);
    $this->in= new BufferedInputStream($this->mem, 10);
  }

  #[@test]
  public function readAll() {
    $this->assertEquals(self::BUFFER, $this->in->read(strlen(self::BUFFER)));
    $this->assertEquals(0, $this->in->available());
  }

  #[@test]
  public function readChunk() {
    $this->assertEquals('Hello', $this->in->read(5));
    $this->assertEquals(5, $this->in->available());   // Five buffered bytes
  }
  
  #[@test]
  public function readChunks() {
    $this->assertEquals('Hello', $this->in->read(5));
    $this->assertEquals(5, $this->in->available());   // Five buffered bytes
    $this->assertEquals(' Worl', $this->in->read(5));
    $this->assertNotEquals(0, $this->in->available());   // Buffer completely empty, but underlying stream has bytes
  }

  #[@test]
  public function closingTwiceHasNoEffect() {
    $this->in->close();
    $this->in->close();
  }

  #[@test]
  public function readSize() {
    $this->assertEquals('Hello Worl', $this->in->read(10));
    $this->assertEquals(strlen(self::BUFFER) - 10, $this->in->available());
  }
}
