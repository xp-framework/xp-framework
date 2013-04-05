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
     * Method to determine if a value would break the constructor of Number
     *
     * @param mixed value
     * @return boolean
     */
    protected function isNumeric($value) {
      try {
        Long::valueOf($value);
      } catch (IllegalArgumentException $e) {
        return FALSE;
      }
      return TRUE;
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

    /**
     * Tests that numeric strings are still recognized as numbers
     *
     */
    #[@test]
    public function shortNumericStringIsANumber() {
      $long= new Long('1');
      $this->assertEquals(1, $long->intValue());
    }

    /**
     * Tests that long numeric strings are still recognized as numbers
     *
     */
    #[@test]
    public function longNumericStringIsANumber() {
      $long1= new Long('12389192458912430951248958921958154');
      $long2= new Long('-12389192458912430951248958921958154');
      $this->assertEquals('12389192458912430951248958921958154', $long1->hashCode());
      $this->assertEquals('-12389192458912430951248958921958154', $long2->hashCode());
    }

    /**
     * Tests that 0 is a number
     *
     */
    #[@test]
    public function zeroIsANumber() {
      $long= new Long(0);
      $this->assertEquals(0, $long->intValue());
    }

    /**
     * Tests that prefixed numbers are recognized as numeric
     *
     */
    #[@test]
    public function prefixedNumbersAreNumbers() {
      $long1= new Long(-1);
      $long2= new Long(+1);
      $this->assertEquals(-1, $long1->intValue());
      $this->assertEquals(1, $long2->intValue());
    }

    /**
     * Tests that exponent notations are recognized as numeric
     *
     */
    #[@test]
    public function exponentNotationIsANumber() {
      $double1= new Double('1e4');
      $double2= new Double('1E4');
      $double3= new Double('1e-4');
      $double4= new Double('1E-4');
      $double5= new Double('-1e4');
      $double6= new Double('-1E4');
      $double7= new Double('-1e-4');
      $double8= new Double('-1E-4');

      $this->assertEquals(1e4, $double1->doubleValue());
      $this->assertEquals(1E4, $double2->doubleValue());
      $this->assertEquals(1e-4, $double3->doubleValue());
      $this->assertEquals(1E-4, $double4->doubleValue());
      $this->assertEquals(-1e4, $double5->doubleValue());
      $this->assertEquals(-1E4, $double6->doubleValue());
      $this->assertEquals(-1e-4, $double7->doubleValue());
      $this->assertEquals(-1E-4, $double8->doubleValue());
    }

    /**
     * Tests that floating notations are recognized as numeric
     *
     */
    #[@test]
    public function floatingNotationIsANumber() {
      $double1= new Double(1.5);
      $double2= new Double(0.5);
      $double3= new Double(.5);

      $this->assertEquals(1.5, $double1->doubleValue());
      $this->assertEquals(0.5, $double2->doubleValue());
      $this->assertEquals(0.5, $double3->doubleValue());
    }

    /**
     * Tests that leading spaces are ignored if the value is numeric
     *
     */
    #[@test]
    public function numberWithLeadingSpaceIsANumber() {
      $long1= new Long( 123);
      $long2= new Long(' 123');
      $this->assertEquals(123, $long1->intValue());
      $this->assertEquals(123, $long2->intValue());
    }

    /**
     * Tests that hex numbers are recognized as numeric
     *
     */
    #[@test]
    public function hexNumbersAreNumbers() {
      $long= new Long('0xAAAA');
      $this->assertEquals(43690, $long->intValue());
    }

    /**
     * Tests that strings are not a number
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringIsNotANumber() {
      Long::valueOf('string');
    }

    /**
     * Tests that booleans are not a number
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function booleanIsNotANumber() {
      Long::valueOf(TRUE);
    }

    /**
     * Tests that NULL is not a number
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function nullIsNotANumber() {
      Long::valueOf(NULL);
    }

    /**
     * Tests that written numbers not numbers
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function writtenNumberIsNotANumber() {
      Long::valueOf('one');
    }

    /**
     * Tests that comma delimiters are not allowed in numbers
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function commaNotationIsNotANumber() {
      Long::valueOf('1,1');
    }

    /**
     * Tests that incomplete exponent notations are not a number
     *
     */
    #[@test]
    public function brokenExponentNotationIsNotANumber() {
      $tests= array('1E+', '1E-', '1E', 'E+4', 'E-4', 'E4', 'E');
      foreach ($tests as $test) {
        if ($this->isNumeric($test))
          $this->fail('Was determined as numeric: '.$test, 'successful', 'lang.IllegalArgumentException');
      }
    }

    /**
     * Tests that multiple leading signs, e.g. dots or prefixes are not
     * recognized as numeric
     *
     */
    #[@test]
    public function doubleLeadingSignsAreNotNumeric() {
      $tests= array('..5', '--1', '++1');
      foreach ($tests as $test) {
        if ($this->isNumeric($test))
          $this->fail('Was determined as numeric: '.$test, 'successful', 'lang.IllegalArgumentException');
      }
    }

    /**
     * Tests that leading letters are not numeric
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function leadingLetterIsNotANumber() {
      Long::valueOf('a123');
    }

    /**
     * Tests that currency values are not numeric
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function currencyValueIsNotANumber() {
      Long::valueOf('$44.00');
    }

    /**
     * Tests that whitespace separated numbers are not numeric
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function whitespaceSeparatedNumbersAreNotNumeric() {
      Long::valueOf('4 4');
    }
  }
?>
