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
    public function differentPrimitives() {
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
    public function differentObjects() {
      $this->assertMessageEquals(
        '(equals) { expected: [lang.types.String:] but was: [lang.types.String:abc] }',
        new AssertionFailedError('equals', new String('abc'), new String(''))
      );
    }
  }
?>
