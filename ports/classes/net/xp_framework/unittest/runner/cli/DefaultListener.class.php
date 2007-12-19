<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestListener', 'io.streams.OutputStreamWriter');

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
     * Called when a test finished successfully.
     *
     * @param   unittest.TestSuccess success
     */
    public function testSucceeded(TestSuccess $success) {
      $this->out->write('.');
    }
    
    /**
     * Called when a test is not run - usually because it is skipped
     * due to a non-met prerequisite or if it has been ignored by using
     * the @ignore annotation.
     *
     * @param   unittest.TestSkipped skipped
     */
    public function testSkipped(TestSkipped $skipped) {
      $this->out->write('S');
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
        $result->count() - $result->skipCount(),
        $result->count(),
        $result->skipCount(),
        $result->successCount(),
        $result->failureCount()
      );
    }
  }
?>
