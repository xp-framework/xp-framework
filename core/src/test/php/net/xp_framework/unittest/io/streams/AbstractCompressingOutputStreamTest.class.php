<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'lang.types.Bytes',
    'io.streams.MemoryOutputStream'
  );

  /**
   * Abstract base class for all compressing output stream tests
   *
   */
  abstract class AbstractCompressingOutputStreamTest extends TestCase {
  
    /**
     * Get extension we depend on
     *
     * @return  string
     */
    protected abstract function extension();

    /**
     * Get stream
     *
     * @param   io.streams.OutputStream wrapped
     * @return  int level
     * @return  io.streams.OutputStream
     */
    protected abstract function newStream(OutputStream $wrapped, $level);

    /**
     * Compress data
     *
     * @param   string in
     * @return  int level
     * @return  string
     */
    protected abstract function compress($in, $level);

    /**
     * Asserts compressed data equals. Used lang.types.Bytes objects in
     * comparison to prevent binary data from appearing in assertion 
     * failure message.
     *
     * @param   string expected
     * @param   string actual
     * @throws  unittest.AssertionFailedError
     */
    protected function assertCompressedDataEquals($expected, $actual) {
      $this->assertEquals(new Bytes($expected), new Bytes($actual));
    }
  
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
     * Test single write
     *
     */
    #[@test]
    public function singleWrite() {
      $out= new MemoryOutputStream();
      $compressor= $this->newStream($out, 6);
      $compressor->write('Hello');
      $compressor->close();
      $this->assertCompressedDataEquals($this->compress('Hello', 6), $out->getBytes());
    }

    /**
     * Test multiple consecutive writes
     *
     */
    #[@test]
    public function multipeWrites() {
      $out= new MemoryOutputStream();
      $compressor= $this->newStream($out, 6);
      $compressor->write('Hello');
      $compressor->write(' ');
      $compressor->write('World');
      $compressor->close();
      $this->assertCompressedDataEquals($this->compress('Hello World', 6), $out->getBytes());
    }

    /**
     * Test highest level of compression (9)
     *
     */
    #[@test]
    public function highestLevel() {
      $out= new MemoryOutputStream();
      $compressor= $this->newStream($out, 9);
      $compressor->write('Hello');
      $compressor->close();
      $this->assertCompressedDataEquals($this->compress('Hello', 9), $out->getBytes());
    }

    /**
     * Test lowest level of compression (1)
     *
     */
    #[@test]
    public function lowestLevel() {
      $out= new MemoryOutputStream();
      $compressor= $this->newStream($out, 1);
      $compressor->write('Hello');
      $compressor->close();
      $this->assertCompressedDataEquals($this->compress('Hello', 1), $out->getBytes());
    }

    /**
     * Test level may not be larger than 10
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function levelTooHigh() {
      $this->newStream(new MemoryOutputStream() , 10);
    }
 
     /**
     * Test level may not be smaller than 0
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function levelTooLow() {
      $this->newStream(new MemoryOutputStream(), -1);
    }

    /**
     * Test closing a stream right after creation
     *
     */
    #[@test]
    public function closingRightAfterCreation() {
      $compressor= $this->newStream(new MemoryOutputStream(), 1);
      $compressor->close();
    }

    /**
     * Test closing a stream twice has no effect.
     *
     * @see   xp://lang.Closeable#close
     */
    #[@test]
    public function closingTwice() {
      $compressor= $this->newStream(new MemoryOutputStream(), 1);
      $compressor->close();
      $compressor->close();
    }
  }
?>
