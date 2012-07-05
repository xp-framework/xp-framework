<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestCase');

  /**
   * TestCase
   *
   * @see   xp://unittest.AssertionFailedError
   */
  class ComparisonTest extends TestCase {

    /**
     * Helper method
     *
     * @param   var expected
     * @param   var actual
     * @return  string
     */
    protected function compare($expected, $actual) {
      return create(new AssertionFailedError('', $actual, $expected))->formatDifference();
    }

    /**
     * Test strings
     *
     */
    #[@test]
    public function stringWithCommonBeginning() {
      $prefix= str_repeat('0123456789', 10);

      $this->assertEquals(
        'expected: "...3abc" but was: "...Hello Worlddef"',
        $this->compare($prefix.'3abc', $prefix.'Hello Worlddef')
      );
    }

    /**
     * Test strings
     *
     */
    #[@test]
    public function stringWithCommonEnding() {
      $postfix= str_repeat('012345678', 10);

      $this->assertEquals(
        'expected: "3..." but was: "Hello World..."',
        $this->compare('3'.$postfix, 'Hello World'.$postfix)
      );
    }

    /**
     * Test strings
     *
     */
    #[@test]
    public function stringWithCommonBeginningAndEnding() {
      $prefix= str_repeat('0123456789', 10);
      $postfix= str_repeat('012345678', 10);

      $this->assertEquals(
        'expected: "...3..." but was: "...Hello World..."',
        $this->compare($prefix.'3'.$postfix, $prefix.'Hello World'.$postfix)
      );
    }

    /**
     * Test two strings
     *
     */
    #[@test]
    public function twoStrings() {
      $this->assertEquals(
        'expected: "Hello" but was: "World"',
        $this->compare('Hello', 'World')
      );
    }

    /**
     * Test two integers
     *
     */
    #[@test]
    public function twoIntegers() {
      $this->assertEquals(
        'expected: 3 but was: 1',
        $this->compare(3, 1)
      );
    }

    /**
     * Test two doubles
     *
     */
    #[@test]
    public function twoDoubles() {
      $this->assertEquals(
        'expected: 3.1 but was: 1.1',
        $this->compare(3.1, 1.1)
      );
    }

    /**
     * Test two bools
     *
     */
    #[@test]
    public function twoBools() {
      $this->assertEquals(
        'expected: true but was: false',
        $this->compare(TRUE, FALSE)
      );
    }

    /**
     * Test two integer arrays
     *
     */
    #[@test]
    public function twoArrays() {
      $this->assertEquals(
        "expected: [\n  0 => 1\n  1 => 2\n] but was: [\n  0 => 2\n  1 => 3\n]",
        $this->compare(array(1, 2), array(2, 3))
      );
    }

    /**
     * Test string vs null
     *
     */
    #[@test]
    public function twoObjects() {
      $this->assertEquals(
        'expected: unittest.TestCase<a> but was: unittest.TestCase<b>',
        $this->compare(new TestCase('a'), new TestCase('b'))
      );
    }
    /**
     * Test string vs int
     *
     */
    #[@test]
    public function stringVsInt() {
      $this->assertEquals(
        'expected: string<"3"> but was: int<3>',
        $this->compare('3', 3)
      );
    }

    /**
     * Test string vs double
     *
     */
    #[@test]
    public function stringVsDouble() {
      $this->assertEquals(
        'expected: string<"3"> but was: double<3>',
        $this->compare('3', 3.0)
      );
    }

    /**
     * Test string vs null
     *
     */
    #[@test]
    public function stringVsNull() {
      $this->assertEquals(
        'expected: string<"null"> but was: null',
        $this->compare('null', NULL)
      );
    }
  }
?>
