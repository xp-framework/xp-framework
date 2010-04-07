<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'io.streams.StringReader',
    'io.streams.MemoryInputStream'
  );

  /**
   * Test StringReader
   *
   * @see      xp://io.streams.StringReader
   * @purpose  Test case
   */
  class StringReaderTest extends TestCase {

    /**
     * Test readLine()
     *
     */
    #[@test]
    public function readLine() {
      $line1= 'This is a test';
      $line2= 'Onother line!';
      
      $stream= new StringReader(new MemoryInputStream($line1."\n".$line2));

      $this->assertEquals($line1, $stream->readLine());
      $this->assertEquals($line2, $stream->readLine());
    }
    
    /**
     * Test readLine() with empty string
     *
     */
    #[@test]
    public function readLineWithEmptyLine() {
      $stream= new StringReader(new MemoryInputStream("\n"));

      $this->assertEquals('', $stream->readLine());
    }

    /**
     * Test readLine() with empty string
     *
     */
    #[@test]
    public function readLineWithEmptyLines() {
      $stream= new StringReader(new MemoryInputStream("\n\n\nHello\n\n"));

      $this->assertEquals('', $stream->readLine());
      $this->assertEquals('', $stream->readLine());
      $this->assertEquals('', $stream->readLine());
      $this->assertEquals('Hello', $stream->readLine());
      $this->assertEquals('', $stream->readLine());
    }
    
    /**
     * Test readLine() with single line
     *
     */
    #[@test]
    public function readLineWithSingleLine() {
      $stream= new StringReader(new MemoryInputStream($line= 'This is a test'));

      $this->assertEquals($line, $stream->readLine());
    }
    
    /**
     * Test readLine() to not break when characters occur which
     * evaluates to FALSE
     *
     */
    #[@test]
    public function readLineWithZeros() {
      $stream= new StringReader(new MemoryInputStream($line= 'Line containing 0 characters'));
      
      $this->assertEquals($line, $stream->readLine());
    }

    /**
     * Test read()
     *
     */
    #[@test]
    public function read() {
      $stream= new StringReader(new MemoryInputStream($line= 'Hello World'));
      
      $this->assertEquals('Hello', $stream->read(5));
      $this->assertEquals(' ', $stream->read(1));
      $this->assertEquals('World', $stream->read(5));
    }

    /**
     * Test read()
     *
     */
    #[@test]
    public function readAll() {
      $stream= new StringReader(new MemoryInputStream($line= 'Hello World'));
      
      $this->assertEquals('Hello World', $stream->read());
    }

    /**
     * Test read()
     *
     */
    #[@test]
    public function readAfterReadingAll() {
      $stream= new StringReader(new MemoryInputStream($line= 'Hello World'));
      
      $this->assertEquals('Hello World', $stream->read());
      $this->assertEquals(NULL, $stream->read());
    }

    /**
     * Test readLine()
     *
     */
    #[@test]
    public function readLineAfterReadingAllLines() {
      $stream= new StringReader(new MemoryInputStream($line= 'Hello World'."\n"));
      
      $this->assertEquals('Hello World', $stream->readLine());
      $this->assertEquals(NULL, $stream->readLine());
    }

    /**
     * Test readLine()
     *
     */
    #[@test]
    public function readAfterReadingAllLines() {
      $stream= new StringReader(new MemoryInputStream($line= 'Hello World'."\n"));
      
      $this->assertEquals('Hello World', $stream->readLine());
      $this->assertEquals(NULL, $stream->read());
    }
  }
?>
