<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * To intercept certain events during a test run, add a listener to
   * the test suite before calling its run() or runTest() methods.
   *
   * @test     xp://net.xp_framework.unittest.tests.ListenerTest
   * @see      xp://unittest.TestSuite#addListener
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
     * Called when a test errors.
     *
     * @param   unittest.TestFailure error
     */
    public function testError(TestError $error);

    /**
     * Called when a test raises warnings.
     *
     * @param   unittest.TestWarning warning
     */
    public function testWarning(TestWarning $warning);
    
    /**
     * Called when a test finished successfully.
     *
     * @param   unittest.TestSuccess success
     */
    public function testSucceeded(TestSuccess $success);

    /**
     * Called when a test is not run because it is skipped due to a 
     * failed prerequisite.
     *
     * @param   unittest.TestSkipped skipped
     */
    public function testSkipped(TestSkipped $skipped);

    /**
     * Called when a test is not run because it has been ignored by using
     * the @ignore annotation.
     *
     * @param   unittest.TestSkipped ignore
     */
    public function testNotRun(TestSkipped $ignore);

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
