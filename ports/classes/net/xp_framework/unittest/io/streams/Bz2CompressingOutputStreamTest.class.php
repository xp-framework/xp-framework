<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'lang.types.Bytes',
    'io.streams.Bz2CompressingOutputStream',
    'io.streams.MemoryOutputStream'
  );

  /**
   * TestCase
   *
   * @ext      bz2
   * @see      xp://io.streams.Bz2CompressingOutputStream
   */
  class Bz2CompressingOutputStreamTest extends TestCase {
  
    /**
     * Setup method. Ensure ext/bz2 is available
     *
     */
    public function setUp() {
      if (!Runtime::getInstance()->extensionAvailable('bz2')) {
        throw new PrerequisitesNotMetError('BZip2 support not available', NULL, array('ext/bz2'));
      }
    }
  
    /**
     * Test single write
     *
     */
    #[@test]
    public function singleWrite() {
      $out= new MemoryOutputStream();
      $compressor= new Bz2CompressingOutputStream($out, 6);
      $compressor->write('Hello');
      $compressor->close();
      $this->assertEquals(new Bytes(bzcompress('Hello', 6)), new Bytes($out->getBytes()));
    }

    /**
     * Test multiple consecutice writes
     *
     */
    #[@test]
    public function multipeWrites() {
      $out= new MemoryOutputStream();
      $compressor= new Bz2CompressingOutputStream($out, 6);
      $compressor->write('Hello');
      $compressor->write(' ');
      $compressor->write('World');
      $compressor->close();
      $this->assertEquals(new Bytes(bzcompress('Hello World', 6)), new Bytes($out->getBytes()));
    }
  }
?>
