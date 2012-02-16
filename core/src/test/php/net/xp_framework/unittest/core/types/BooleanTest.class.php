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
    public function falseString() {
      $this->assertEquals(Boolean::$FALSE, new Boolean('false'));
    }

    /**
     * Tests intValue()
     *
     */
    #[@test]
    public function trueIsOne() {
      $this->assertEquals(1, create(new Boolean(TRUE))->intValue());
    }

    /**
     * Tests intValue()
     *
     */
    #[@test]
    public function falseIsZero() {
      $this->assertEquals(0, create(new Boolean(FALSE))->intValue());
    }

    /**
     * Tests
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function numericStringIsNotAValidBoolean() {
      new Boolean('1');
    }
  }
?>
