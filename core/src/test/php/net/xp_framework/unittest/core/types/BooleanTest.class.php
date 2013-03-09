<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestCase', 'lang.types.Boolean');

  /**
   * Tests the boolean wrapper type
   *
   * @see      xp://lang.types.Boolean
   */
  class BooleanTest extends TestCase {

    /**
     * Tests
     *
     */
    #[@test]
    public function trueBoolPrimitiveIsTrue() {
      $this->assertEquals(Boolean::$TRUE, new Boolean(TRUE));
    }

    /**
     * Tests
     *
     */
    #[@test]
    public function falseBoolPrimitiveIsFalse() {
      $this->assertEquals(Boolean::$FALSE, new Boolean(FALSE));
    }

    /**
     * Tests
     *
     */
    #[@test]
    public function oneIntPrimitiveIsTrue() {
      $this->assertEquals(Boolean::$TRUE, new Boolean(1));
    }

    /**
     * Tests
     *
     */
    #[@test]
    public function otherNonZeroIntPrimitiveIsTrue() {
      $this->assertEquals(Boolean::$TRUE, new Boolean(6100));
    }

    /**
     * Tests
     *
     */
    #[@test]
    public function zeroIntPrimitiveIsFalse() {
      $this->assertEquals(Boolean::$FALSE, new Boolean(0));
    }

    /**
     * Tests
     *
     */
    #[@test]
    public function trueString() {
      $this->assertEquals(Boolean::$TRUE, new Boolean('true'));
    }

    /**
     * Tests
     *
     */
    #[@test]
    public function trueStringMixedCase() {
      $this->assertEquals(Boolean::$TRUE, new Boolean('True'));
    }

    /**
     * Tests
     *
     */
    #[@test]
    public function falseString() {
      $this->assertEquals(Boolean::$FALSE, new Boolean('false'));
    }

    /**
     * Tests
     *
     */
    #[@test]
    public function falseStringMixedCase() {
      $this->assertEquals(Boolean::$FALSE, new Boolean('False'));
    }

    /**
     * Tests intValue()
     *
     */
    #[@test]
    public function trueIsOne() {
      $this->assertEquals(1, Boolean::$TRUE->intValue());
    }

    /**
     * Tests intValue()
     *
     */
    #[@test]
    public function falseIsZero() {
      $this->assertEquals(0, Boolean::$FALSE->intValue());
    }

    /**
     * Tests hashCode()
     *
     */
    #[@test]
    public function trueHashCode() {
      $this->assertEquals('true', Boolean::$TRUE->hashCode());
    }

    /**
     * Tests hashCode()
     *
     */
    #[@test]
    public function falseHashCode() {
      $this->assertEquals('false', Boolean::$FALSE->hashCode());
    }

    /**
     * Tests unacceptable values
     *
     */
    #[@test]
    public function numericStringIsAValidBoolean() {
      $this->assertEquals(Boolean::$TRUE, new Boolean('1'));
    }

    /**
     * Tests unacceptable values
     *
     */
    #[@test]
    public function zeroNumericStringIsAValidBoolean() {
      $this->assertEquals(Boolean::$FALSE, new Boolean('0'));
    }

    /**
     * Tests unacceptable values
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function emptyStringIsNotAValidBoolean() {
      new Boolean('');
    }

    /**
     * Tests unacceptable values
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function misspelledFalse() {
      new Boolean('fals3');
    }

    /**
     * Tests unacceptable values
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function doublePrimitiveIsNotAValidBoolean() {
      new Boolean(1.0);
    }
  }
?>
