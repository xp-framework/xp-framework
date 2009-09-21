<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'io.streams.StringWriter',
    'io.streams.MemoryOutputStream'
  );

  /**
   * Test StringReader
   *
   * @see      xp://io.streams.StringReader
   * @purpose  Test case
   */
  class StringWriterTest extends TestCase {

    /**
     * Test write()
     *
     */
    #[@test]
    public function write() {
      $stream= new StringWriter($out= new MemoryOutputStream());
      $stream->write($data= 'This is a test');
      
      $this->assertEquals($data, $out->getBytes());
    }
    
    /**
     * Test writef()
     *
     */
    #[@test]
    public function writef() {
      $stream= new StringWriter($out= new MemoryOutputStream());
      $stream->writef('Some string: %s, some int: %d', 'test', 6100);
      
      $this->assertEquals('Some string: test, some int: 6100', $out->getBytes());
    }
    
    /**
     * Test writeLine()
     *
     */
    #[@test]
    public function writeLine() {
      $stream= new StringWriter($out= new MemoryOutputStream());
      $stream->writeLine($line= 'This is the first line');
      
      $this->assertEquals($line."\n", $out->getBytes());
    }
    
    /**
     * Test writeLinef()
     *
     */
    #[@test]
    public function writeLinef() {
      $stream= new StringWriter($out= new MemoryOutputStream());
      $stream->writeLinef('This %s the %d line', 'is', 1);
      
      $this->assertEquals("This is the 1 line\n", $out->getBytes());
    }
  }
?>
