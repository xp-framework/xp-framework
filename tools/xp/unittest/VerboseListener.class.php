<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestListener', 'io.streams.OutputStreamWriter', 'lang.Runtime');

  /**
   * Verbose listener - shows details for all tests (succeeded, failed
   * and skipped/ignored).
   *
   * @purpose  TestListener
   */
  class VerboseListener extends Object implements TestListener {
    public
      $out= NULL;
    
    /**
     * Constructor
     *
     * @param   io.streams.OutputStreamWriter out
     */
    public function __construct(OutputStreamWriter $out) {
      $this->out= $out;
    }
    
    /**
     * Called when a test case starts.
     *
     * @param   unittest.TestCase failure
     */
    public function testStarted(TestCase $case) {
      // NOOP
    }

    /**
     * Called when a test fails.
     *
     * @param   unittest.TestFailure failure
     */
    public function testFailed(TestFailure $failure) {
      $this->out->write('F');
    }

    /**
     * Called when a test errors.
     *
     * @param   unittest.TestFailure error
     */
    public function testError(TestFailure $error) {
      $this->out->write('E');
    }
    
    /**
     * Called when a test finished successfully.
     *
     * @param   unittest.TestSuccess success
     */
    public function testSucceeded(TestSuccess $success) {
      $this->out->write('.');
    }
    
    /**
     * Called when a test is not run because it is skipped due to a 
     * failed prerequisite.
     *
     * @param   unittest.TestSkipped skipped
     */
    public function testSkipped(TestSkipped $skipped) {
      $this->out->write('S');
    }

    /**
     * Called when a test is not run because it has been ignored by using
     * the @ignore annotation.
     *
     * @param   unittest.TestSkipped ignore
     */
    public function testNotRun(TestSkipped $ignore) {
      $this->out->write('N');
    }

    /**
     * Called when a test run starts.
     *
     * @param   unittest.TestSuite suite
     */
    public function testRunStarted(TestSuite $suite) {
      $this->out->writeLine('===> Running test suite (', $suite->numTests(), ' test(s))');
    }
    
    /**
     * Called when a test run finishes.
     *
     * @param   unittest.TestSuite suite
     * @param   unittest.TestResult result
     */
    public function testRunFinished(TestSuite $suite, TestResult $result) {

      // Details
      if ($result->successCount() > 0) {
        $this->out->writeLine("\n---> Succeeeded:");
        foreach (array_keys($result->succeeded) as $key) {
          $this->out->writeLine('* ', $result->succeeded[$key]);
        }
      }
      if ($result->skipCount() > 0) {
        $this->out->writeLine("\n---> Skipped:");
        foreach (array_keys($result->skipped) as $key) {
          $this->out->writeLine('* ', $result->skipped[$key]);
        }
      }
      if ($result->failureCount() > 0) {
        $this->out->writeLine("\n---> Failed:");
        foreach (array_keys($result->failed) as $key) {
          $this->out->writeLine('* ', $result->failed[$key]);
        }
      }

      $this->out->writeLinef(
        "\n===> %s: %d run (%d skipped), %d succeeded, %d failed",
        $result->failureCount() ? 'FAIL' : 'OK',
        $result->runCount(),
        $result->skipCount(),
        $result->successCount(),
        $result->failureCount()
      );
      $this->out->writeLinef(
        '===> Memory used: %.2f kB (%.2f kB peak)',
        Runtime::getInstance()->memoryUsage() / 1024,
        Runtime::getInstance()->peakMemoryUsage() / 1024
      );
    }
  }
?>
