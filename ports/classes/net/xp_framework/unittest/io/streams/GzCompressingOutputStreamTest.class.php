<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'lang.types.Bytes',
    'io.streams.GzCompressingOutputStream',
    'io.streams.MemoryOutputStream'
  );

  /**
   * TestCase
   *
   * @ext      zlib
   * @see      xp://io.streams.Bz2CompressingOutputStream
   */
  class GzCompressingOutputStreamTest extends TestCase {
  
    /**
     * Setup method. Ensure ext/zlib is available
     *
     */
    public function setUp() {
      if (!Runtime::getInstance()->extensionAvailable('zlib')) {
        throw new PrerequisitesNotMetError('ZLIB support not available', NULL, array('ext/zlib'));
      }
    }
    
    /**
     * Asserts GZ-encoded data equals. Ignores the first 10 bytes - the
     * GZIP header, which will change every time due to current Un*x 
     * timestamp being embedded therein.
     *
     * @param   string expected
     * @param   string actual
     * @throws  unittest.AssertionFailedErrot
     */
    protected function assertGzDataEquals($expected, $actual) {
      $this->assertEquals(new Bytes(substr($expected, 10)), new Bytes(substr($actual, 10)));
    }
  
    /**
     * Test single write
     *
     */
    #[@test]
    public function singleWrite() {
      $out= new MemoryOutputStream();
      $compressor= new GzCompressingOutputStream($out, 6);
      $compressor->write('Hello');
      $compressor->close();
      $this->assertGzDataEquals(gzencode('Hello', 6), $out->getBytes());
    }

    /**
     * Test multiple consecutice writes
     *
     */
    #[@test]
    public function multipeWrites() {
      $out= new MemoryOutputStream();
      $compressor= new GzCompressingOutputStream($out, 6);
      $compressor->write('Hello');
      $compressor->write(' ');
      $compressor->write('World');
      $compressor->close();
      $this->assertGzDataEquals(gzencode('Hello World', 6), $out->getBytes());
    }
  }
?>
