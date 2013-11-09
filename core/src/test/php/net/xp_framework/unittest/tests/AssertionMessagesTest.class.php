<?php namespace net\xp_framework\unittest\tests;

use unittest\TestCase;
use unittest\AssertionFailedError;

/**
 * TestCase
 *
 * @see   xp://unittest.AssertionFailedError
 */
class AssertionMessagesTest extends TestCase {

  /**
   * Assertion helper
   *
   * @param   string expected
   * @param   unittest.AssertionFailedError error
   * @throws  unittest.AssertionFailedError
   */
  protected function assertMessageEquals($expected, $error) {
    $this->assertEquals(
      "unittest.AssertionFailedError { ".$expected." }\n",
      $error->compoundMessage()
    );
  }

  #[@test]
  public function differentIntegerPrimitives() {
    $this->assertMessageEquals(
      'expected [2] but was [1] using: \'equals\'',
      new AssertionFailedError('equals', 1, 2)
    );
  }

  #[@test]
  public function differentBoolPrimitives() {
    $this->assertMessageEquals(
      'expected [true] but was [false] using: \'equals\'',
      new AssertionFailedError('equals', false, true)
    );
  }

  #[@test]
  public function differentPrimitives() {
    $this->assertMessageEquals(
      'expected [integer:2] but was [double:2] using: \'equals\'',
      new AssertionFailedError('equals', 2.0, 2)
    );
  }

  #[@test]
  public function differentStrings() {
    $this->assertMessageEquals(
      'expected [abc] but was [] using: \'equals\'',
      new AssertionFailedError('equals', new \lang\types\String(''), new \lang\types\String('abc'))
    );
  }

  /**
   * Test two strings
   *
   */
  #[@test]
  public function differentStringPrimitives() {
    $this->assertMessageEquals(
      'expected ["Hello"] but was ["World"] using: \'equals\'',
      new AssertionFailedError('equals', 'World', 'Hello')
    );
  }

  #[@test]
  public function stringAndStringPrimitive() {
    $this->assertMessageEquals(
      'expected [lang.types.String:] but was [string:""] using: \'equals\'',
      new AssertionFailedError('equals', '', new \lang\types\String(''))
    );
  }

  #[@test]
  public function differentTypes() {
    $this->assertMessageEquals(
      'expected [] but was [net.xp_framework.unittest.tests.AssertionMessagesTest<differentTypes>] using: \'equals\'',
      new AssertionFailedError('equals', $this, new \lang\types\String(''))
    );
  }

  /**
   * Test two integer arrays
   *
   */
  #[@test]
  public function twoArrays() {
    $this->assertMessageEquals(
      "expected [[\n  0 => 1\n  1 => 2\n]] but was [[\n  0 => 2\n  1 => 3\n]] using: 'equals'",
      new AssertionFailedError('equals', array(2, 3), array(1, 2))
    );
  }

  /**
   * Test two objects of the same type
   *
   */
  #[@test]
  public function twoObjects() {
    $this->assertMessageEquals(
      "expected [unittest.TestCase<a>] but was [unittest.TestCase<b>] using: 'equals'",
      new AssertionFailedError('equals', new TestCase('b'), new TestCase('a'))
    );
  }

  /**
   * Test NULL
   *
   */
  #[@test]
  public function nullVsObject() {
    $this->assertMessageEquals(
      "expected [unittest.TestCase:unittest.TestCase<b>] but was [null] using: 'equals'",
      new AssertionFailedError('equals', null, new TestCase('b'))
    );
  }

  /**
   * Test NULL
   *
   */
  #[@test]
  public function nullVsString() {
    $this->assertMessageEquals(
      "expected [string:\"NULL\"] but was [null] using: 'equals'",
      new AssertionFailedError('equals', null, 'NULL')
    );
  }

  #[@test]
  public function differentStringsWithCommonLeadingPart() {
    $prefix= str_repeat('*', 100);
    $this->assertMessageEquals(
      'expected ["...def"] but was ["...abc"] using: \'equals\'',
      new AssertionFailedError('equals', $prefix.'abc', $prefix.'def')
    );
  }

  #[@test]
  public function differentStringsWithCommonTrailingPart() {
    $postfix= str_repeat('*', 100);
    $this->assertMessageEquals(
      'expected ["def..."] but was ["abc..."] using: \'equals\'',
      new AssertionFailedError('equals', 'abc'.$postfix, 'def'.$postfix)
    );
  }

  #[@test]
  public function differentStringsWithCommonLeadingAndTrailingPart() {
    $prefix= str_repeat('<', 100);
    $postfix= str_repeat('>', 100);
    $this->assertMessageEquals(
      'expected ["...def..."] but was ["...abc..."] using: \'equals\'',
      new AssertionFailedError('equals', $prefix.'abc'.$postfix, $prefix.'def'.$postfix)
    );
  }

  #[@test]
  public function prefixShorterThanContextLength() {
    $this->assertMessageEquals(
      'expected ["abc!"] but was ["abc."] using: \'equals\'',
      new AssertionFailedError('equals', 'abc.', 'abc!')
    );
  }

  #[@test]
  public function postfixShorterThanContextLength() {
    $this->assertMessageEquals(
      'expected ["!abc"] but was [".abc"] using: \'equals\'',
      new AssertionFailedError('equals', '.abc', '!abc')
    );
  }
}
