<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'io.streams.InflatingInputStream',
    'io.streams.MemoryInputStream'
  );

  /**
   * TestCase
   *
   * @ext      zlib
   * @see      xp://io.streams.InflatingInputStream
   */
  class InflatingInputStreamTest extends TestCase {

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
     * Test single read
     *
     */
    #[@test]
    public function singleRead() {
      $in= new MemoryInputStream(gzdeflate('Hello', 6));
      $deflater= new InflatingInputStream($in);
      $chunk= $deflater->read();
      $deflater->close();
      $this->assertEquals('Hello', $chunk);
    }

    /**
     * Test single read
     *
     */
    #[@test]
    public function multipleReads() {
      $in= new MemoryInputStream(gzdeflate('Hello World', 6));
      $deflater= new InflatingInputStream($in);
      $chunk1= $deflater->read(5);
      $chunk2= $deflater->read(1);
      $chunk3= $deflater->read(5);
      $deflater->close();
      $this->assertEquals('Hello', $chunk1);
      $this->assertEquals(' ', $chunk2);
      $this->assertEquals('World', $chunk3);
    }
  }
?>
