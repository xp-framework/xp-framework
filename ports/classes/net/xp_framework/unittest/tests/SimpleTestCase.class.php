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
     * Sets up this test. Throws a PrerequisitesNotMetError if the "skipped" 
     * test is run.
     *
     */
    public function setUp() {
      if ('skipped' === $this->name) {
        throw new PrerequisitesNotMetError('SKIP', $this->name);
      }
    }

    /**
     * Always succeeds
     *
     */
    #[@test]
    public function succeeds() {
      $this->assertTrue(TRUE);
    }

    /**
     * Always fails
     *
     */
    #[@test]
    public function fails() {
      $this->assertTrue(FALSE);
    }

    /**
     * Always skipped
     *
     */
    #[@test]
    public function skipped() {
      $this->fail('Prerequisites not met, should not be executed');
    }

    /**
     * Always ignored
     *
     */
    #[@test, @ignore('For test purposes')]
    public function ignored() {
    }

    /**
     * A test that timeouts
     *
     */
    #[@test, @limit(time= 0.010)]
    public function timeouts() {
      $start= gettimeofday();
      $end= (1000000 * $start['sec']) + $start['usec'] + 1000 * 50;    // 0.05 seconds
      do {
        $now= gettimeofday();
      } while ((1000000 * $now['sec']) + $now['usec'] < $end);
    }

    /**
     * A test that does not timeout
     *
     */
    #[@test, @limit(time= 1.0)]
    public function noTimeout() {
    }
  }
?>
