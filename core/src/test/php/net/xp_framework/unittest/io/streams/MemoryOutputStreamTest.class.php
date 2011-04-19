<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'io.streams.MemoryOutputStream'
  );

  /**
   * Unit tests for streams API
   *
   * @see      xp://io.streams.OutputStream
   * @purpose  Unit test
   */
  class MemoryOutputStreamTest extends TestCase {
    protected $out= NULL;
  
    /**
     * Setup method. Creates the fixture.
     *
     */
    public function setUp() {
      $this->out= new MemoryOutputStream();
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

    /**
     * Test closing a stream twice has no effect.
     *
     * @see   xp://lang.Closeable#close
     */
    #[@test]
    public function closingTwice() {
      $this->out->close();
      $this->out->close();
    }
  }
?>
