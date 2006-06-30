<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('util.profiling.unittest.TestCase');

  /**
   * This class is used in the SuiteTest class' running* methods.
   *
   * @see      xp://net.xp_framework.unittest.tests.SuiteTest
   * @purpose  Unit Test
   */
  class SimpleTestCase extends TestCase {

    /**
     * Always succeeds
     *
     * @access  public
     */
    #[@test]
    function succeeds() {
      $this->assertTrue(TRUE);
    }

    /**
     * Always fails
     *
     * @access  public
     */
    #[@test]
    function fails() {
      $this->assertTrue(FALSE);
    }
  }
?>
