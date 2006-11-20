<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('unittest.TestCase');

  /**
   * This class is used in the SuiteTest class' running* methods and
   * by the LimitTest methods
   *
   * @see      xp://net.xp_framework.unittest.tests.SuiteTest
   * @see      xp://net.xp_framework.unittest.tests.LimitTest
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

    /**
     * A test that timeouts
     *
     * @access  public
     */
    #[@test, @limit(time= 0.010)]
    function timeouts() {
      $start= gettimeofday();
      $end= (1000000 * $start['sec']) + $start['usec'] + 1000 * 50;    // 0.05 seconds
      do {
        $now= gettimeofday();
      } while ((1000000 * $now['sec']) + $now['usec'] < $end);
    }

    /**
     * A test that does not timeout
     *
     * @access  public
     */
    #[@test, @limit(time= 1.0)]
    function noTimeout() {
    }
  }
?>
