<?php
/* This class is part of the XP framework
 *
 * $Id: StreamTest.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace net::xp_framework::unittest::io;

  ::uses(
    'unittest.TestCase',
    'io.Stream'
  );

  /**
   * Unit tests for io.Stream class
   *
   * @see      xp://io.Stream
   * @purpose  Unit test
   */
  class StreamTest extends unittest::TestCase {
  
    /**
     * Test basic write operations.
     *
     */
    #[@test]
    public function testWrite() {
      $s= new io::Stream();
      $s->open(STREAM_MODE_READWRITE);
      $this->assertEquals($s->write('Foo'), 3);
      $this->assertEquals($s->buffer, 'Foo');
      $this->assertEquals($s->writeLine('Foo'), 4);
    }
    
    /**
     * Test read operation
     *
     */
    #[@test]
    public function testRead() {
      $s= new io::Stream();
      $s->open(STREAM_MODE_READWRITE);
      $s->writeLine('Pellentesque sapien enim, pellentesque sed.');
      $s->writeLine('Sed et tortor suscipit velit.');
      $s->writeLine('Phasellus at metus quis erat.');
      
      $this->assertEquals($s->read(), '');
      $this->assertEquals($s->tell(), 104);
      
      // Rewind and read each line
      $s->rewind();
      $this->assertEquals($s->readLine(), 'Pellentesque sapien enim, pellentesque sed.');
      $this->assertEquals($s->readLine(), 'Sed et tortor suscipit velit.');
      $this->assertEquals($s->readLine(), 'Phasellus at metus quis erat.');
      
      // No more lines left, we should receive an empty string
      $this->assertEquals($s->readLine(), '');
      
      // Check that the offset is still at the "end" of the stream
      $this->assertEquals($s->tell(), 104);
    }
    
    /**
     * Test overwriting parts of the stream
     *
     */
    #[@test]
    public function testOverwrite() {
      $s= new io::Stream();
      $s->open(STREAM_MODE_READWRITE);
      $s->writeLine('Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Curabitur aliquam.');
      $s->seek(20);
      $s->writeLine('Lorem ipsum dolor sit amet.');
      $this->assertEquals(
        $s->buffer,
        "Lorem ipsum dolor siLorem ipsum dolor sit amet.\ning elit. Curabitur aliquam.\n"
      );
    }
    
    /**
     * Test seeking
     *
     */
    #[@test]
    public function testSeekTell() {
      $s= new io::Stream();
      $s->open(STREAM_MODE_READWRITE);
      $this->assertEquals($s->tell(), 0, 'wrong start position');
      $this->assertEquals($s->size(), 0, 'wrong initial size');
      
      $s->writeLine('Foo Bar Baz');
      $this->assertEquals($s->tell(), 12, 'wrong position');
      $this->assertEquals($s->size(), 12, 'wrong size');
    }
    
    /**
     * Test truncating
     *
     */
    #[@test]
    public function testTruncate() {
      $s= new io::Stream();
      $s->open(STREAM_MODE_READWRITE);
      $s->write('This is a beautiful example stream.');
      
      // Truncating to a longer stream does not work...
      $this->assertFalse($s->truncate(1000));
      
      // ... only truncating to smaller size
      $this->assertTrue($s->truncate(10));
      $this->assertEquals($s->size(), 10);
      $this->assertEquals($s->tell(), 10);
      $this->assertEquals($s->buffer, 'This is a ');
    }
    
    /**
     * Test that the file pointer is rewound when a stream is closed
     * and reopened
     *
     */
    #[@test]
    public function positionAfterReOpen() {
      $s= new io::Stream();
      $s->open(STREAM_MODE_WRITE);
      $s->write('GIF89a');
      $s->close();
      $s->open(STREAM_MODE_READ);
      $this->assertEquals(0, $s->tell());
      $this->assertEquals('GIF89a', $s->read());
      $s->close();
    }
  }
?>
