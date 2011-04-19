<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'lang.types.Bytes',
    'io.streams.MemoryInputStream'
  );

  /**
   * Abstract base class for all compressing output stream tests
   *
   */
  abstract class AbstractDecompressingInputStreamTest extends TestCase {
  
    /**
     * Get extension we depend on
     *
     * @return  string
     */
    protected abstract function extension();

    /**
     * Get stream
     *
     * @param   io.streams.InputStream wrapped
     * @return  io.streams.InputStream
     */
    protected abstract function newStream(InputStream $wrapped);

    /**
     * Compress data
     *
     * @param   string in
     * @return  int level
     * @return  string
     */
    protected abstract function compress($in, $level);

    /**
     * Setup method. Ensure extension we depend on is available
     *
     */
    public function setUp() {
      $depend= $this->extension();
      if (!Runtime::getInstance()->extensionAvailable($depend)) {
        throw new PrerequisitesNotMetError(ucfirst($depend).' support not available', NULL, array('ext/'.$depend));
      }
    }
  
    /**
     * Test single read
     *
     */
    #[@test]
    public function singleRead() {
      $in= new MemoryInputStream($this->compress('Hello', 6));
      $decompressor= $this->newStream($in);
      $chunk= $decompressor->read();
      $decompressor->close();
      $this->assertEquals(new Bytes('Hello'), $chunk);
    }

    /**
     * Test multiple consecutive reads
     *
     */
    #[@test]
    public function multipleReads() {
      $in= new MemoryInputStream($this->compress('Hello World', 6));
      $decompressor= $this->newStream($in);
      $chunk1= $decompressor->read(5);
      $chunk2= $decompressor->read(1);
      $chunk3= $decompressor->read(5);
      $decompressor->close();
      $this->assertEquals(new Bytes('Hello'), $chunk1);
      $this->assertEquals(new Bytes(' '), $chunk2);
      $this->assertEquals(new Bytes('World'), $chunk3);
    }

    /**
     * Test highest level of compression (9)
     *
     */
    #[@test]
    public function highestLevel() {
      $in= new MemoryInputStream($this->compress('Hello', 9));
      $decompressor= $this->newStream($in);
      $chunk= $decompressor->read();
      $decompressor->close();
      $this->assertEquals(new Bytes('Hello'), $chunk);
    }

    /**
     * Test highest level of compression (1)
     *
     */
    #[@test]
    public function lowestLevel() {
      $in= new MemoryInputStream($this->compress('Hello', 1));
      $decompressor= $this->newStream($in);
      $chunk= $decompressor->read();
      $decompressor->close();
      $this->assertEquals(new Bytes('Hello'), $chunk);
    }

    /**
     * Test closing a stream right after creation
     *
     */
    #[@test]
    public function closingRightAfterCreation() {
      $decompressor= $this->newStream(new MemoryInputStream($this->compress('Hello', 1)));
      $decompressor->close();
    }

    /**
     * Test closing a stream twice has no effect.
     *
     * @see   xp://lang.Closeable#close
     */
    #[@test]
    public function closingTwice() {
      $decompressor= $this->newStream(new MemoryInputStream($this->compress('Hello', 1)));
      $decompressor->close();
      $decompressor->close();
    }
  }
?>
