<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestListener',
    'io.streams.OutputStreamWriter',
    'lang.Runtime'
  );

  /**
   * Default listener - only shows details for failed tests.
   *
   * @purpose  TestListener
   */
  class DefaultListener extends Object implements TestListener {
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
     * @param   unittest.TestError error
     */
    public function testError(TestError $error) {
      $this->out->write('E');
    }

    /**
     * Called when a test raises warnings.
     *
     * @param   unittest.TestWarning warning
     */
    public function testWarning(TestWarning $warning) {
      $this->out->write('W');
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
      $this->out->write('[');
    }
    
    /**
     * Called when a test run finishes.
     *
     * @param   unittest.TestSuite suite
     * @param   unittest.TestResult result
     */
    public function testRunFinished(TestSuite $suite, TestResult $result) {
      $this->out->writeLine(']');
      
      // Show failed test details
      if ($result->failureCount() > 0) {
        $this->out->writeLine();
        foreach ($result->failed as $failure) {
          $this->out->writeLine('F ', $failure);
        }
      }

      $this->out->writeLinef(
        "\n%s: %d/%d run (%d skipped), %d succeeded, %d failed",
        $result->failureCount() ? 'FAIL' : 'OK',
        $result->runCount(),
        $result->count(),
        $result->skipCount(),
        $result->successCount(),
        $result->failureCount()
      );
      $this->out->writeLinef(
        'Memory used: %.2f kB (%.2f kB peak)',
        Runtime::getInstance()->memoryUsage() / 1024,
        Runtime::getInstance()->peakMemoryUsage() / 1024
      );
    }
  }
?>
