<?php namespace net\xp_framework\unittest\core\types;

use unittest\TestCase;
use lang\types\Boolean;


/**
 * Tests the boolean wrapper type
 *
 * @see      xp://lang.types.Boolean
 */
class BooleanTest extends TestCase {

  #[@test]
  public function trueBoolPrimitiveIsTrue() {
    $this->assertEquals(Boolean::$TRUE, new Boolean(true));
  }

  #[@test]
  public function falseBoolPrimitiveIsFalse() {
    $this->assertEquals(Boolean::$FALSE, new Boolean(false));
  }

  #[@test]
  public function oneIntPrimitiveIsTrue() {
    $this->assertEquals(Boolean::$TRUE, new Boolean(1));
  }

  #[@test]
  public function otherNonZeroIntPrimitiveIsTrue() {
    $this->assertEquals(Boolean::$TRUE, new Boolean(6100));
  }

  #[@test]
  public function zeroIntPrimitiveIsFalse() {
    $this->assertEquals(Boolean::$FALSE, new Boolean(0));
  }

  #[@test]
  public function trueString() {
    $this->assertEquals(Boolean::$TRUE, new Boolean('true'));
  }

  #[@test]
  public function trueStringMixedCase() {
    $this->assertEquals(Boolean::$TRUE, new Boolean('True'));
  }

  #[@test]
  public function falseString() {
    $this->assertEquals(Boolean::$FALSE, new Boolean('false'));
  }

  #[@test]
  public function falseStringMixedCase() {
    $this->assertEquals(Boolean::$FALSE, new Boolean('False'));
  }

  #[@test]
  public function trueIsOne() {
    $this->assertEquals(1, Boolean::$TRUE->intValue());
  }

  #[@test]
  public function falseIsZero() {
    $this->assertEquals(0, Boolean::$FALSE->intValue());
  }

  #[@test]
  public function trueHashCode() {
    $this->assertEquals('true', Boolean::$TRUE->hashCode());
  }

  #[@test]
  public function falseHashCode() {
    $this->assertEquals('false', Boolean::$FALSE->hashCode());
  }

  #[@test]
  public function numericStringIsAValidBoolean() {
    $this->assertEquals(Boolean::$TRUE, new Boolean('1'));
  }

  #[@test]
  public function zeroNumericStringIsAValidBoolean() {
    $this->assertEquals(Boolean::$FALSE, new Boolean('0'));
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function emptyStringIsNotAValidBoolean() {
    new Boolean('');
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function misspelledFalse() {
    new Boolean('fals3');
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function doublePrimitiveIsNotAValidBoolean() {
    new Boolean(1.0);
  }
}
