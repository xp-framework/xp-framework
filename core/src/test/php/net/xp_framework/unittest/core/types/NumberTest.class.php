<?php namespace net\xp_framework\unittest\core\types;

use lang\types\Long;
use lang\types\Byte;
use lang\types\Short;
use lang\types\Integer;
use lang\types\Float;
use lang\types\Double;

/**
 * Tests the number wrapper typess
 *
 * @see   xp://lang.types.Number
 * @see   xp://lang.types.Byte
 * @see   xp://lang.types.Short
 * @see   xp://lang.types.Integer
 * @see   xp://lang.types.Double
 * @see   xp://lang.types.Float
 */
class NumberTest extends \unittest\TestCase {

  /**
   * Tests a given type
   *
   * @param   lang.types.Number number
   * @param   int int
   * @param   float float
   */
  protected function testType(\lang\types\Number $number, $int, $float) {
    $this->assertEquals($int, $number->intValue(), 'intValue');
    $this->assertEquals($float, $number->floatValue(), 'floatValue');
    $this->assertEquals($number, clone($number), 'clone');
  }

  #[@test]
  public function longType() {
    $this->testType(new Long(0), 0, 0.0);
  }

  #[@test]
  public function byteType() {
    $this->testType(new Byte(0), 0, 0.0);
  }

  #[@test]
  public function shortType() {
    $this->testType(new Short(0), 0, 0.0);
  }

  #[@test]
  public function integerType() {
    $this->testType(new Integer(0), 0, 0.0);
  }

  #[@test]
  public function doubleType() {
    $this->testType(new Double(0), 0, 0.0);
  }

  #[@test]
  public function floatType() {
    $this->testType(new Float(0), 0, 0.0);
  }

  #[@test]
  public function an_integer_is_not_a_long() {
    $this->assertNotEquals(new Integer(1), new Long(1));
  }

  #[@test]
  public function a_byte_is_not_a_short() {
    $this->assertNotEquals(new Byte(1), new Short(1));
  }

  #[@test]
  public function a_double_is_not_a_float() {
    $this->assertNotEquals(new Double(1.0), new Float(1.0));
  }

  #[@test]
  public function shortNumericStringIsANumber() {
    $long= new Long('1');
    $this->assertEquals(1, $long->intValue());
  }

  #[@test, @values(['12389192458912430951248958921958154', '-12389192458912430951248958921958154'])]
  public function longNumericStringIsANumber($string) {
    $this->assertEquals($string, create(new Long($string))->hashCode());
  }

  #[@test]
  public function zeroIsANumber() {
    $this->assertEquals(0, create(new Long(0))->intValue());
  }

  #[@test, @values([['-1', -1], ['+1', 1]])]
  public function prefixedNumbersAreNumbers($arg, $num) {
    $this->assertEquals($num, create(new Long($arg))->intValue());
  }

  #[@test, @values([['1e4', 1e4], ['1E4', 1e4], ['1e-4', 1e-4], ['1E-4', 1e-4]])]
  public function numbers_with_exponent_notation($arg, $num) {
    $this->assertEquals($num, create(new Double($arg))->doubleValue());
  }

  #[@test, @values([['-1e4', -1e4], ['-1E4', -1e4], ['-1e-4', -1e-4], ['-1E-4', -1e-4]])]
  public function negative_numbers_with_exponent_notation($arg, $num) {
    $this->assertEquals($num, create(new Double($arg))->doubleValue());
  }

  #[@test, @values([['1.5', 1.5], ['+0.5', +0.5], ['-0.5', -0.5], ['0.0', 0.0]])]
  public function floatingNotationIsANumber($arg, $num) {
    $this->assertEquals($num, create(new Double($arg))->doubleValue());
  }

  #[@test]
  public function numberWithLeadingSpaceIsANumber() {
    $this->assertEquals(123, create(new Long(' 123'))->intValue());
  }

  #[@test]
  public function hexNumbersAreNumbers() {
    $long= new Long('0xAAAA');
    $this->assertEquals(43690, $long->intValue());
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function stringIsNotANumber() {
    Long::valueOf('string');
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function booleanIsNotANumber() {
    Long::valueOf(true);
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function nullIsNotANumber() {
    Long::valueOf(null);
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function writtenNumberIsNotANumber() {
    Long::valueOf('one');
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function commaNotationIsNotANumber() {
    Long::valueOf('1,1');
  }

  #[@test, @values(['1E+', '1E-', '1E', 'E+4', 'E-4', 'E4', 'E']), @expect('lang.IllegalArgumentException')]
  public function brokenExponentNotationIsNotANumber($value) {
    Long::valueOf($value);
  }

  #[@test, @values(['..5', '--1', '++1']), @expect('lang.IllegalArgumentException')]
  public function doubleLeadingSignsAreNotNumeric($value) {
    Long::valueOf($value);
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function leadingLetterIsNotANumber() {
    Long::valueOf('a123');
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function currencyValueIsNotANumber() {
    Long::valueOf('$44.00');
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function whitespaceSeparatedNumbersAreNotNumeric() {
    Long::valueOf('4 4');
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function floatingPointNumberIsNotLong() {
    Long::valueOf(4.4);
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function floatingPointInStringIsNotLong() {
    Long::valueOf('4.4');
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function floatingPointNumberIsNotInteger() {
    Integer::valueOf(4.4);
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function floatingPointInStringIsNotInteger() {
    Integer::valueOf('4.4');
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function floatingPointNumberIsNotShort() {
    Short::valueOf(4.4);
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function floatingPointInStringIsNotShort() {
    Short::valueOf('4.4');
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function floatingPointNumberIsNotByte() {
    Byte::valueOf(4.4);
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function floatingPointInStringIsNotByte() {
    Byte::valueOf('4.4');
  }
}
