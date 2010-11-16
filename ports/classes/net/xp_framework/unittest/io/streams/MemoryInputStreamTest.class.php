<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'io.streams.MemoryInputStream'
  );

  /**
   * Unit tests for streams API
   *
   * @see      xp://io.streams.InputStream
   * @purpose  Unit test
   */
  class MemoryInputStreamTest extends TestCase {
    const BUFFER= 'Hello World, how are you doing?';

    protected $in= NULL;
  
    /**
     * Setup method. Creates the fixture.
     *
     */
    public function setUp() {
      $this->in= new MemoryInputStream(self::BUFFER);
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
     * Test reading a five byte chunk
     *
     */
    #[@test]
    public function readChunk() {
      $this->assertEquals(new Bytes('Hello'), $this->in->read(5));
      $this->assertEquals(strlen(self::BUFFER)- 5, $this->in->available());
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
