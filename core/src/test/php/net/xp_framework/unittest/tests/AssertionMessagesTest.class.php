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
     * Test
     *
     */
    #[@test]
    public function differentPrimitives() {
      $this->assertEquals(
        "unittest.AssertionFailedError (==) { expected: [integer:2] but was: [integer:1] }\n",
        create(new AssertionFailedError('==', 1, 2))->compoundMessage()
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function differentObjects() {
      $this->assertEquals(
        "unittest.AssertionFailedError (equals) { expected: [lang.types.String:] but was: [lang.types.String:abc] }\n",
        create(new AssertionFailedError('equals', new String('abc'), new String('')))->compoundMessage()
      );
    }
  }
?>
