<?php
/* This class is part of the XP framework
 *
 * $Id: NumberTest.class.php 8974 2006-12-27 17:29:09Z friebe $
 */

  namespace net::xp_framework::unittest::core::types;

  ::uses(
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
  class NumberTest extends unittest::TestCase {
    public
      $number = NULL;

    /**
     * Tests a given type
     *
     * @param   &lang.types.Number number
     * @param   int int
     * @param   float float
     */
    protected function testType($number, $int, $float) {
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
      $this->testType(new lang::types::Long(0), 0, 0.0);
    }

    /**
     * Tests the Byte class
     *
     * @see     xp://lang.types.Byte
     */
    #[@test]
    public function byteType() {
      $this->testType(new lang::types::Byte(0), 0, 0.0);
    }

    /**
     * Tests the Short class
     *
     * @see     xp://lang.types.Short
     */
    #[@test]
    public function shortType() {
      $this->testType(new lang::types::Short(0), 0, 0.0);
    }

    /**
     * Tests the Integer class
     *
     * @see     xp://lang.types.Integer
     */
    #[@test]
    public function IntegerType() {
      $this->testType(new lang::types::Integer(0), 0, 0.0);
    }

    /**
     * Tests the Double class
     *
     * @see     xp://lang.types.Double
     */
    #[@test]
    public function doubleType() {
      $this->testType(new lang::types::Double(0), 0, 0.0);
    }

    /**
     * Tests the Float class
     *
     * @see     xp://lang.types.Float
     */
    #[@test]
    public function floatType() {
      $this->testType(new lang::types::Float(0), 0, 0.0);
    }
  }
?>
