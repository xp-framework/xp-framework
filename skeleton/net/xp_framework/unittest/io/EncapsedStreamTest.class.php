<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.profiling.unittest.TestCase',
    'io.EncapsedStream'
  );

  /**
   * TestCase
   *
   * @see      xp://io.EncapsedStream
   * @purpose  Testcase
   */
  class EncapsedStreamTest extends TestCase {
    var
      $s      = NULL,
      $stream = NULL;
      
    /**
     * Sets up test case
     *
     * @access  public
     */
    function setUp() {
      $this->stream= &new Stream();
      $this->stream->open(STREAM_MODE_WRITE);
      $this->stream->write('1234567890');
      $this->stream->close();
      $this->stream->rewind();
      $this->stream->open(STREAM_MODE_READ);
      $this->s= &new EncapsedStream($this->stream, 1, 8);
    }
    
    /**
     * Test
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalStateException')]
    function testInvalidConstruct() {
      $s= &new EncapsedStream(new Stream(), 0, 0);
    }
    
    /**
     * Test
     *
     * @access  public
     */
    #[@test]
    function testOpen() {
      $this->s->open(STREAM_MODE_READ);
    }
    
    /**
     * Test
     *
     * @access  public
     */
    #[@test]
    function testRead() {
      $this->assertEquals('23456789', $this->s->readLine());
    }
    
    /**
     * Test
     *
     * @access  public
     */
    #[@test]
    function testGets() {
      $this->assertEquals('23456789', $this->s->gets());
    }
    
    /**
     * Test
     *
     * @access  public
     */
    #[@test]
    function testSeek() {
      $this->s->seek(6);
      $this->assertEquals('89', $this->s->read());
    }
    
    /**
     * Test
     *
     * @access  public
     */
    #[@test]
    function testEof() {
      $this->s->seek(6);
      $this->assertFalse($this->s->eof());
      $this->s->seek(8);
      $this->assertTrue($this->s->eof());
    }    
  }
?>
