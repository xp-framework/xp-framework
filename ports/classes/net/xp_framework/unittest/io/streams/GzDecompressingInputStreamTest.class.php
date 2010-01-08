<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'io.streams.GzDecompressingInputStream',
    'io.streams.MemoryInputStream'
  );

  /**
   * TestCase
   *
   * @ext      Gz
   * @see      xp://io.streams.GzDecompressingInputStream
   */
  class GzDecompressingInputStreamTest extends TestCase {

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
     * Test single read
     *
     */
    #[@test]
    public function singleRead() {
      $in= new MemoryInputStream(gzencode('Hello', 6));
      $decompressor= new GzDecompressingInputStream($in);
      $chunk= $decompressor->read();
      $decompressor->close();
      $this->assertEquals('Hello', $chunk);
    }

    /**
     * Test single read
     *
     */
    #[@test]
    public function multipleReads() {
      $in= new MemoryInputStream(gzencode('Hello World', 6));
      $decompressor= new GzDecompressingInputStream($in);
      $chunk1= $decompressor->read(5);
      $chunk2= $decompressor->read(1);
      $chunk3= $decompressor->read(5);
      $decompressor->close();
      $this->assertEquals('Hello', $chunk1);
      $this->assertEquals(' ', $chunk2);
      $this->assertEquals('World', $chunk3);
    }
  }
?>
