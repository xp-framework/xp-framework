<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestListener',
    'io.streams.OutputStreamWriter'
  );

  /**
   * XTerm Title listener
   * --------------------
   * Updates the window title bar of an xterm or xterm-compatible shell
   * window. This listener has no options.
   */
  class XTermTitleListener extends Object implements TestListener {
    const PROGRESS_WIDTH= 20;
    private $out= NULL;
    private $cur, $sum;

    /**
     * Constructor
     *
     * @param   io.streams.OutputStreamWriter out
     */
    public function __construct(OutputStreamWriter $out) {
      $this->out= $out;
    }

    /**
     * Write status of currently executing test case
     *
     * @param   unittest.TestCase case
     */
    private function writeStatus(TestCase $case) {
      $this->cur++;

      $perc= floor($this->cur / $this->sum * self::PROGRESS_WIDTH);

      $this->out->writef("\033]2;Running: [%s%s] %s::%s()\007",
        str_repeat('*', $perc), str_repeat('-', self::PROGRESS_WIDTH- $perc),
        $case->getClassName(),
        $case->getName()
      );
    }

    /**
     * Called when a test case starts.
     *
     * @param   unittest.TestCase failure
     */
    public function testStarted(TestCase $case) {
      $this->writeStatus($case);
    }

    /**
     * Called when a test fails.
     *
     * @param   unittest.TestFailure failure
     */
    public function testFailed(TestFailure $failure) {
    }

    /**
     * Called when a test errors.
     *
     * @param   unittest.TestError error
     */
    public function testError(TestError $error) {
    }

    /**
     * Called when a test raises warnings.
     *
     * @param   unittest.TestWarning warning
     */
    public function testWarning(TestWarning $warning) {
    }

    /**
     * Called when a test finished successfully.
     *
     * @param   unittest.TestSuccess success
     */
    public function testSucceeded(TestSuccess $success) {
    }

    /**
     * Called when a test is not run because it is skipped due to a
     * failed prerequisite.
     *
     * @param   unittest.TestSkipped skipped
     */
    public function testSkipped(TestSkipped $skipped) {
    }

    /**
     * Called when a test is not run because it has been ignored by using
     * the @ignore annotation.
     *
     * @param   unittest.TestSkipped ignore
     */
    public function testNotRun(TestSkipped $ignore) {
    }

    /**
     * Called when a test run starts.
     *
     * @param   unittest.TestSuite suite
     */
    public function testRunStarted(TestSuite $suite) {
      $this->sum= $suite->numTests();
      $this->cur= 0;
    }

    /**
     * Called when a test run finishes.
     *
     * @param   unittest.TestSuite suite
     * @param   unittest.TestResult result
     */
    public function testRunFinished(TestSuite $suite, TestResult $result) {
      $this->out->writef(
        "\033]2;%s: %d/%d run (%d skipped), %d succeeded, %d failed\007",
        $result->failureCount() > 0 ? 'FAIL' : 'OK',
        $result->runCount(),
        $result->count(),
        $result->skipCount(),
        $result->successCount(),
        $result->failureCount()
      );
    }
  }
?>
