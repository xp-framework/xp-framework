<?php
/* This class is part of the XP framework
 *
 * $Id: MemoryOutputStreamTest.class.php 8963 2006-12-27 14:21:05Z friebe $ 
 */

  namespace net::xp_framework::unittest::io::streams;

  ::uses(
    'unittest.TestCase',
    'io.streams.MemoryOutputStream'
  );

  /**
   * Unit tests for streams API
   *
   * @see      xp://io.streams.OutputStream
   * @purpose  Unit test
   */
  class MemoryOutputStreamTest extends unittest::TestCase {
    protected $out= NULL;
  
    /**
     * Setup method. Creates the fixture.
     *
     */
    public function setUp() {
      $this->out= new io::streams::MemoryOutputStream();
    }
  
    /**
     * Test string writing
     *
     */
    #[@test]
    public function writeString() {
      $this->out->write('Hello');
      $this->assertEquals('Hello', $this->out->getBytes());
    }

    /**
     * Test number writing
     *
     */
    #[@test]
    public function writeNumber() {
      $this->out->write(5);
      $this->assertEquals('5', $this->out->getBytes());
    }
  }
?>
