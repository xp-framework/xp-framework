<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'io.archive.zip.Compression'
  );

  /**
   * TestCase for compression enumeration
   *
   * @see      xp://io.archive.zip.Compression
   */
  class CompressionTest extends TestCase {
  
    /**
     * Test getInstance() method
     *
     */
    #[@test]
    public function noneInstance() {
      $this->assertEquals(Compression::$NONE, Compression::getInstance(0));
    }

    /**
     * Test getInstance() method
     *
     */
    #[@test]
    public function gzInstance() {
      $this->assertEquals(Compression::$GZ, Compression::getInstance(8));
    }

    /**
     * Test getInstance() method
     *
     */
    #[@test]
    public function bzInstance() {
      $this->assertEquals(Compression::$BZ, Compression::getInstance(12));
    }

    /**
     * Test getInstance() method
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function unknownInstance() {
      Compression::getInstance(-1);
    }
  }
?>
