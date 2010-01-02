<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'io.streams.DeflatingOutputStream',
    'io.streams.MemoryOutputStream'
  );

  /**
   * TestCase
   *
   * @ext      zlib
   * @see      xp://io.streams.DeflatingOutputStream
   */
  class DeflatingOutputStreamTest extends TestCase {
  
    /**
     * Test single write
     *
     */
    #[@test]
    public function singleWrite() {
      $out= new MemoryOutputStream();
      $deflater= new DeflatingOutputStream($out, 6);
      $deflater->write('Hello');
      $deflater->close();
      $this->assertEquals(gzdeflate('Hello', 6), $out->getBytes());
    }

    /**
     * Test multiple consecutice writes
     *
     */
    #[@test]
    public function multipeWrites() {
      $out= new MemoryOutputStream();
      $deflater= new DeflatingOutputStream($out, 6);
      $deflater->write('Hello');
      $deflater->write(' ');
      $deflater->write('World');
      $deflater->close();
      $this->assertEquals(gzdeflate('Hello World', 6), $out->getBytes());
    }
  }
?>
