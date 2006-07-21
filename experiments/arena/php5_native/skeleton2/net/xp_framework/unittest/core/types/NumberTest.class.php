<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'util.profiling.unittest.TestCase', 
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
    public
      $number = NULL;

    /**
     * Tests a given type
     *
     * @access  protected
     * @param   &lang.types.Number number
     * @param   int int
     * @param   float float
     */
    public function testType(&$number, $int, $float) {
      $this->assertEquals($int, $number->intValue(), 'intValue') &&
      $this->assertEquals($float, $number->floatValue(), 'floatValue') &&
      $this->assertEquals($number, clone($number), 'clone');
    }

    /**
     * Tests the Long class
     *
     * @see     xp://lang.types.Long
     * @access  public
     */
    #[@test]
    public function longType() {
      $this->testType(new Long(0), 0, 0.0);
    }

    /**
     * Tests the Byte class
     *
     * @see     xp://lang.types.Byte
     * @access  public
     */
    #[@test]
    public function byteType() {
      $this->testType(new Byte(0), 0, 0.0);
    }

    /**
     * Tests the Short class
     *
     * @see     xp://lang.types.Short
     * @access  public
     */
    #[@test]
    public function shortType() {
      $this->testType(new Short(0), 0, 0.0);
    }

    /**
     * Tests the Integer class
     *
     * @see     xp://lang.types.Integer
     * @access  public
     */
    #[@test]
    public function IntegerType() {
      $this->testType(new Integer(0), 0, 0.0);
    }

    /**
     * Tests the Double class
     *
     * @see     xp://lang.types.Double
     * @access  public
     */
    #[@test]
    public function doubleType() {
      $this->testType(new Double(0), 0, 0.0);
    }

    /**
     * Tests the Float class
     *
     * @see     xp://lang.types.Float
     * @access  public
     */
    #[@test]
    public function floatType() {
      $this->testType(new Float(0), 0, 0.0);
    }
  }
?>
