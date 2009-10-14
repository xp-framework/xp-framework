<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase'
  );

  /**
   * TestCase
   *
   * @see      reference
   * @purpose  purpose
   */
  class SetUpFailingTestCase extends TestCase {
  
    /**
     * Sets up test case - throw an exception not derived from
     * unittest.PrerequisitesNotMetError or unittest.AssertionFailedError
     * which are expected.
     *
     */
    public function setUp() {
      throw new IllegalArgumentException('Something went wrong in setup.');
    }

    /**
     * Run empty test; this will invoke setUp() before
     *
     */
    #[@test]
    public function emptyTest() {
    }
  }
?>
