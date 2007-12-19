<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * To intercept certain events during a test run, add a listener to
   * the test suite before calling its run() or runTest() methods.
   *
   * @see      xp://unittest.TestSuite#addListener
   * @purpose  Listen
   */
  interface TestListener {

    /**
     * Called when a test case starts.
     *
     * @param   unittest.TestCase failure
     */
    public function testStarted(TestCase $case);
  
    /**
     * Called when a test fails.
     *
     * @param   unittest.TestFailure failure
     */
    public function testFailed(TestFailure $failure);
    
    /**
     * Called when a test finished successfully.
     *
     * @param   unittest.TestSuccess success
     */
    public function testSucceeded(TestSuccess $success);

    /**
     * Called when a test is not run - usually because it is skipped
     * due to a non-met prerequisite or if it has been ignored by using
     * the @ignore annotation.
     *
     * @param   unittest.TestSkipped skipped
     */
    public function testSkipped(TestSkipped $skipped);

    /**
     * Called when a test run starts.
     *
     * @param   unittest.TestSuite suite
     */
    public function testRunStarted(TestSuite $suite);
    
    /**
     * Called when a test run finishes.
     *
     * @param   unittest.TestSuite suite
     * @param   unittest.TestResult result
     */
    public function testRunFinished(TestSuite $suite, TestResult $result);

  }
?>
