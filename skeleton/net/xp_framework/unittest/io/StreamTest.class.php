<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.profiling.unittest.TestCase',
    'io.Stream'
  );

  /**
   * Unit tests for io.Stream class
   *
   * @see      xp://io.Stream
   * @purpose  Unit test
   */
  class StreamTest extends TestCase {
  
    /**
     * Test basic write operations.
     *
     * @access  public
     */
    #[@test]
    function testWrite() {
      $s= &new Stream();
      $s->open(STREAM_MODE_READWRITE);
      $this->assertEquals($s->write('Foo'), 3);
      $this->assertEquals($s->buffer, 'Foo');
      $this->assertEquals($s->writeLine('Foo'), 4);
    }
    
    /**
     * Test read operation
     *
     * @access  public
     */
    #[@test]
    function testRead() {
      $s= &new Stream();
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
     * @access  public
     */
    #[@test]
    function testOverwrite() {
      $s= &new Stream();
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
     * @access  public
     */
    #[@test]
    function testSeekTell() {
      $s= &new Stream();
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
     * @access  public
     */
    #[@test]
    function testTruncate() {
      $s= &new Stream();
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
  }
?>
