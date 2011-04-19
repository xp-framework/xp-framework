<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'lang.types.Long',
    'lang.types.Byte',
    'lang.types.Short',
    'lang.types.Integer',
    'lang.types.Float',
    'lang.types.Double'
  );

  /**
   * Tests the number wrapper typess
   *
   * @see      xp://lang.types.Number
   * @purpose  Testcase
   */
  class NumberTest extends TestCase {

    /**
     * Tests a given type
     *
     * @param   lang.types.Number number
     * @param   int int
     * @param   float float
     */
    protected function testType(Number $number, $int, $float) {
      $this->assertEquals($int, $number->intValue(), 'intValue');
      $this->assertEquals($float, $number->floatValue(), 'floatValue');
      $this->assertEquals($number, clone($number), 'clone');
    }

    /**
     * Tests the Long class
     *
     * @see     xp://lang.types.Long
     */
    #[@test]
    public function longType() {
      $this->testType(new Long(0), 0, 0.0);
    }

    /**
     * Tests the Byte class
     *
     * @see     xp://lang.types.Byte
     */
    #[@test]
    public function byteType() {
      $this->testType(new Byte(0), 0, 0.0);
    }

    /**
     * Tests the Short class
     *
     * @see     xp://lang.types.Short
     */
    #[@test]
    public function shortType() {
      $this->testType(new Short(0), 0, 0.0);
    }

    /**
     * Tests the Integer class
     *
     * @see     xp://lang.types.Integer
     */
    #[@test]
    public function integerType() {
      $this->testType(new Integer(0), 0, 0.0);
    }

    /**
     * Tests the Double class
     *
     * @see     xp://lang.types.Double
     */
    #[@test]
    public function doubleType() {
      $this->testType(new Double(0), 0, 0.0);
    }

    /**
     * Tests the Float class
     *
     * @see     xp://lang.types.Float
     */
    #[@test]
    public function floatType() {
      $this->testType(new Float(0), 0, 0.0);
    }

    /**
     * Tests different wrapper types are not equal
     *
     */
    #[@test]
    public function differentTypesNotEqual() {
      $this->assertNotEquals(new Integer(1), new Long(1), 'integer = long');
      $this->assertNotEquals(new Byte(1), new Short(1), 'byte = short');
      $this->assertNotEquals(new Double(1.0), new Float(1.0), 'double = float');
    }
  }
?>
