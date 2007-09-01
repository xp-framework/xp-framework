<?php
/* This class is part of the XP framework
 *
 * $Id: MemoryInputStreamTest.class.php 8963 2006-12-27 14:21:05Z friebe $ 
 */

  namespace net::xp_framework::unittest::io::streams;

  ::uses(
    'unittest.TestCase',
    'io.streams.MemoryInputStream'
  );

  /**
   * Unit tests for streams API
   *
   * @see      xp://io.streams.InputStream
   * @purpose  Unit test
   */
  class MemoryInputStreamTest extends unittest::TestCase {
    const BUFFER= 'Hello World, how are you doing?';

    protected $in= NULL;
  
    /**
     * Setup method. Creates the fixture.
     *
     */
    public function setUp() {
      $this->in= new io::streams::MemoryInputStream(self::BUFFER);
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
     * Test reading a five byte chunk
     *
     */
    #[@test]
    public function readChunk() {
      $this->assertEquals('Hello', $this->in->read(5));
      $this->assertEquals(strlen(self::BUFFER)- 5, $this->in->available());
    }
    
  }
?>
