<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'unittest.AssertionFailedError'
  );

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
        "unittest.AssertionFailedError ".$expected."\n",
        $error->compoundMessage()
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function differentIntegerPrimitives() {
      $this->assertMessageEquals(
        '(==) { expected: [integer:2] but was: [integer:1] }',
        new AssertionFailedError('==', 1, 2)
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function differentPrimitives() {
      $this->assertMessageEquals(
        '(==) { expected: [integer:2] but was: [double:2] }',
        new AssertionFailedError('==', 2.0, 2)
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function differentStrings() {
      $this->assertMessageEquals(
        '(equals) { expected: [lang.types.String:] but was: [lang.types.String:abc] }',
        new AssertionFailedError('equals', new String('abc'), new String(''))
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function stringAndStringPrimitive() {
      $this->assertMessageEquals(
        '(equals) { expected: [lang.types.String:] but was: [string:""] }',
        new AssertionFailedError('equals', '', new String(''))
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function differentTypes() {
      $this->assertMessageEquals(
        '(equals) { expected: [lang.types.String:] but was: [net.xp_framework.unittest.tests.AssertionMessagesTest:net.xp_framework.unittest.tests.AssertionMessagesTest<differentTypes>] }',
        new AssertionFailedError('equals', $this, new String(''))
      );
    }
  }
?>
