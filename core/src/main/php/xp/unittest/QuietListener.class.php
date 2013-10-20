<?php namespace xp\unittest;

/**
 * Quiet listener
 * --------------
 * No output at all. This listener has no options.
 */
class QuietListener extends \lang\Object implements \unittest\TestListener {


  /**
   * Called when a test case starts.
   *
   * @param   unittest.TestCase failure
   */
  public function testStarted(\unittest\TestCase $case) {
    // NOOP
  }

  /**
   * Called when a test fails.
   *
   * @param   unittest.TestFailure failure
   */
  public function testFailed(\unittest\TestFailure $failure) {
    // NOOP
  }

  /**
   * Called when a test errors.
   *
   * @param   unittest.TestError error
   */
  public function testError(\unittest\TestError $error) {
    // NOOP
  }

  /**
   * Called when a test raises warnings.
   *
   * @param   unittest.TestWarning warning
   */
  public function testWarning(\unittest\TestWarning $warning) {
    // NOOP
  }
  
  /**
   * Called when a test finished successfully.
   *
   * @param   unittest.TestSuccess success
   */
  public function testSucceeded(\unittest\TestSuccess $success) {
    // NOOP
  }
  
  /**
   * Called when a test is not run because it is skipped due to a 
   * failed prerequisite.
   *
   * @param   unittest.TestSkipped skipped
   */
  public function testSkipped(\unittest\TestSkipped $skipped) {
    // NOOP
  }

  /**
   * Called when a test is not run because it has been ignored by using
   * the @ignore annotation.
   *
   * @param   unittest.TestSkipped ignore
   */
  public function testNotRun(\unittest\TestSkipped $ignore) {
    // NOOP
  }

  /**
   * Called when a test run starts.
   *
   * @param   unittest.TestSuite suite
   */
  public function testRunStarted(\unittest\TestSuite $suite) {
    // NOOP
  }
  
  /**
   * Called when a test run finishes.
   *
   * @param   unittest.TestSuite suite
   * @param   unittest.TestResult result
   */
  public function testRunFinished(\unittest\TestSuite $suite, \unittest\TestResult $result) {
    // NOOP
  }
}
