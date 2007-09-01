<?php
/* This class is part of the XP framework
 *
 * $Id: EncapsedStreamTest.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace net::xp_framework::unittest::io;

  ::uses(
    'unittest.TestCase',
    'io.EncapsedStream',
    'io.Stream'
  );

  /**
   * TestCase
   *
   * @see      xp://io.EncapsedStream
   * @purpose  Testcase
   */
  class EncapsedStreamTest extends unittest::TestCase {
    public
      $s      = NULL,
      $stream = NULL;
      
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->stream= new io::Stream();
      $this->stream->open(STREAM_MODE_WRITE);
      $this->stream->write('1234567890');
      $this->stream->close();
      $this->stream->rewind();
      $this->stream->open(STREAM_MODE_READ);
      $this->s= new io::EncapsedStream($this->stream, 1, 8);
    }
    
    /**
     * Test
     *
     */
    #[@test, @expect('lang.IllegalStateException')]
    public function testInvalidConstruct() {
      $s= new io::EncapsedStream(new io::Stream(), 0, 0);
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function testOpen() {
      $this->s->open(STREAM_MODE_READ);
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function testRead() {
      $this->assertEquals('23456789', $this->s->readLine());
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function testGets() {
      $this->assertEquals('23456789', $this->s->gets());
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function testSeek() {
      $this->s->seek(6);
      $this->assertEquals('89', $this->s->read());
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function testEof() {
      $this->s->seek(6);
      $this->assertFalse($this->s->eof());
      $this->s->seek(8);
      $this->assertTrue($this->s->eof());
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function testReadline() {
      $stream= new io::Stream();
      $stream->open(STREAM_MODE_WRITE);
      $stream->writeLine('This is the first line.');
      $stream->writeLine('This is the second line.');
      $stream->writeLine('And there is a third one.');
      $stream->close();
      $stream->open(STREAM_MODE_READ);
      
      $this->s= new io::EncapsedStream($stream, 5, $stream->size()- 35);
      $this->assertEquals('is the first line.', $this->s->readLine());
      $this->assertEquals('This is the second li', $this->s->readLine());
      $this->assertEquals('', $this->s->readLine());
    }
    
  }
?>
