<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.profiling.unittest.TestFailure', 
    'util.profiling.unittest.TestSuccess', 
    'util.profiling.unittest.TestSkipped'
  );

  /**
   * Test result
   *
   * @see      xp://util.profiling.unittest.TestSuite
   * @purpose  Wrapper class
   */
  class TestResult extends Object {
    var
      $succeeded    = array(),
      $failed       = array(),
      $skipped      = array();
      
    /**
     * Mark a test as succeeded
     *
     * @access  public
     * @param   &util.profiling.unittest.TestCase test
     * @param   mixed value
     */
    function setSucceeded(&$test, $value) {
      $this->succeeded[$test->getClassName().'::'.$test->getName()]= &new TestSuccess($value);
    }
    
    /**
     * Mark a test as failed
     *
     * @access  public
     * @param   &util.profiling.unittest.TestCase test
     * @param   mixed reason
     */
    function setFailed(&$test, $reason) {
      $this->failed[$test->getClassName().'::'.$test->getName()]= &new TestFailure($reason);
    }

    /**
     * Mark a test as been skipped
     *
     * @access  public
     * @param   &util.profiling.unittest.TestCase test
     * @param   mixed reason
     */
    function setSkipped(&$test, $reason) {
      $this->skipped[$test->getClassName().'::'.$test->getName()]= &new TestSkipped($reason);
    }
  }
?>
