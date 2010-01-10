<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'security.checksum.CRC32'
  );

  /**
   * TestCase
   *
   * @see      xp://security.checksum.CRC32
   */
  class CRC32Test extends TestCase {
  
    /**
     * Test constructor
     *
     */
    #[@test]
    public function zeroCRCsAreEqual() {
      $this->assertEquals(new CRC32(0), new CRC32('00000000'));
    }

    /**
     * Test constructor
     *
     */
    #[@test]
    public function negativeNumbers() {
      $this->assertEquals(new CRC32(-137262718), new CRC32('f7d18982'));
    }

    /**
     * Test constructor
     *
     */
    #[@test]
    public function nonZeroCRCsAreEqual() {
      $this->assertEquals(new CRC32(1140816021), new CRC32('43ff7895'));
    }

    /**
     * Test asInt32() method
     *
     */
    #[@test]
    public function asInt32() {
      $this->assertEquals(1140816021, create(new CRC32(1140816021))->asInt32());
    }

    /**
     * Test asInt32() method
     *
     */
    #[@test]
    public function asInt32Negative() {
      $this->assertEquals(-137262718, create(new CRC32('f7d18982'))->asInt32());
    }
 
    /**
     * Test getValue() method
     *
     */
    #[@test]
    public function getValue() {
      $this->assertEquals('43ff7895', create(new CRC32(1140816021))->getValue());
    }
  }
?>
