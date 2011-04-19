<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'io.streams.BufferedOutputStream',
    'io.streams.MemoryOutputStream'
  );

  /**
   * Unit tests for streams API
   *
   * @see      xp://io.streams.OutputStream
   * @purpose  Unit test
   */
  class BufferedOutputStreamTest extends TestCase {
    protected 
      $out= NULL,
      $mem= NULL;
    
    /**
     * Setup method. Creates the fixture, a BufferedOutputStream with
     * a buffer size of 10 characters.
     *
     */
    public function setUp() {
      $this->mem= new MemoryOutputStream();
      $this->out= new BufferedOutputStream($this->mem, 10);
    }
  
    /**
     * Test 
     *
     */
    #[@test]
    public function doNotFillBuffer() {
      $this->out->write('Hello');
      $this->assertEquals('', $this->mem->getBytes());
    }

    /**
     * Test 
     *
     */
    #[@test]
    public function fillBuffer() {
      $this->out->write(str_repeat('*', 10));
      $this->assertEquals('', $this->mem->getBytes());
    }

    /**
     * Test 
     *
     */
    #[@test]
    public function overFlowBuffer() {
      $this->out->write('A long string that will fill the buffer');
      $this->assertEquals('A long string that will fill the buffer', $this->mem->getBytes());
    }
  }
?>
