<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'lang.types.Bytes',
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
     * Setup method. Ensure ext/zlib is available
     *
     */
    public function setUp() {
      if (!Runtime::getInstance()->extensionAvailable('zlib')) {
        throw new PrerequisitesNotMetError('ZLib support not available', NULL, array('ext/bz2'));
      }
    }
  
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
      $this->assertEquals(new Bytes(gzdeflate('Hello', 6)), new Bytes($out->getBytes()));
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
      $this->assertEquals(new Bytes(gzdeflate('Hello World', 6)), new Bytes($out->getBytes()));
    }
  }
?>
