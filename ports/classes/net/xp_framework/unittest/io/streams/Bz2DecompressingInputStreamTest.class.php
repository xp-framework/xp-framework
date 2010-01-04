<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'io.streams.Bz2DecompressingInputStream',
    'io.streams.MemoryInputStream'
  );

  /**
   * TestCase
   *
   * @ext      bz2
   * @see      xp://io.streams.Bz2DecompressingInputStream
   */
  class Bz2DecompressingInputStreamTest extends TestCase {

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
     * Test single read
     *
     */
    #[@test]
    public function singleRead() {
      $in= new MemoryInputStream(bzcompress('Hello', 6));
      $deflater= new Bz2DecompressingInputStream($in, 6);
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
      $in= new MemoryInputStream(bzcompress('Hello World', 6));
      $deflater= new Bz2DecompressingInputStream($in, 6);
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
