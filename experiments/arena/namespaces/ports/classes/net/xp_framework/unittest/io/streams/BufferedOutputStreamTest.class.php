<?php
/* This class is part of the XP framework
 *
 * $Id: BufferedOutputStreamTest.class.php 8963 2006-12-27 14:21:05Z friebe $ 
 */

  namespace net::xp_framework::unittest::io::streams;

  ::uses(
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
  class BufferedOutputStreamTest extends unittest::TestCase {
    protected 
      $out= NULL,
      $mem= NULL;
    
    /**
     * Setup method. Creates the fixture, a BufferedOutputStream with
     * a buffer size of 10 characters.
     *
     */
    public function setUp() {
      $this->mem= new io::streams::MemoryOutputStream();
      $this->out= new io::streams::BufferedOutputStream($this->mem, 10);
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
