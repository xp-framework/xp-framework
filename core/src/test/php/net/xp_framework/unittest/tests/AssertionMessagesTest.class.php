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
        "unittest.AssertionFailedError { ".$expected." }\n",
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
        'expected [2] but was [1] using: \'equals\'',
        new AssertionFailedError('equals', 1, 2)
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function differentPrimitives() {
      $this->assertMessageEquals(
        'expected [integer:2] but was [double:2] using: \'equals\'',
        new AssertionFailedError('equals', 2.0, 2)
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function differentStrings() {
      $this->assertMessageEquals(
        'expected [abc] but was [] using: \'equals\'',
        new AssertionFailedError('equals', new String(''), new String('abc'))
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function stringAndStringPrimitive() {
      $this->assertMessageEquals(
        'expected [lang.types.String:] but was [string:""] using: \'equals\'',
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
        'expected [] but was [net.xp_framework.unittest.tests.AssertionMessagesTest<differentTypes>] using: \'equals\'',
        new AssertionFailedError('equals', $this, new String(''))
      );
    }
  }
?>
