<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('unittest.TestCase');

  /**
   * This class is used in the SuiteTest 
   *
   * @purpose  Unit Test
   */
  class AnotherTestCase extends TestCase {

    /**
     * Always succeeds
     *
     */
    #[@test]
    public function succeeds() {
      $this->assertTrue(TRUE);
    }
  }
?>
