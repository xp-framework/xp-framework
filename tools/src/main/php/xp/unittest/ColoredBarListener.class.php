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
   * Colorful verbose test listener
   * 
   * Features:
   * - Updates console with background-colored bar: blue while running
   *   successfully, red if any error has occurred, green when all tests
   *   finished successfully.
   * - Recycles status line
   * - Fast feedback when a TestFailure occurrs (writes stacktrace / test
   *   information out instantly)
   *
   */
  class ColoredBarListener extends Object implements TestListener {
    const PROGRESS_WIDTH= 10;
    private $out= NULL;
    private $cur, $sum, $len, $status;
    private $stats;

    private static $CODE_RED    = "\033[41;1;37m";
    private static $CODE_GREEN  = "\033[42;1;37m";
    private static $CODE_BLUE   = "\033[44;1;37m";
    private static $CODE_RESET  = "\033[0m";

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
    private function writeStatus(TestCase $case= NULL) {
      if (NULL !== $case) {
        $this->cur++;
      }

      $perc= floor($this->cur / $this->sum * self::PROGRESS_WIDTH);
      $out= sprintf(" Running %-3d of %d [%-10s] %01dF %01dE %01dW %01dS %01dN",
        $this->cur,
        $this->sum,
        str_repeat(".", $perc),
        $this->stats['failed'],
        $this->stats['errored'],
        $this->stats['warned'],
        $this->stats['skipped'],
        $this->stats['notrun']
      );

      $out= sprintf('%50s %20s', 
        $out,
        '- '.($this->status ? 'PASSING' : 'FAILURE!').' '
      );

      $this->recycleLine();
      $this->writeColoredLine($out, $this->colorCodeFor($this->status, NULL === $case));
    }

    /**
     * Retrieve color code for status
     *
     * @param   var status
     * @param   bool final
     * @return  string
     */
    private function colorCodeFor($status, $final) {
      // If failure, code is always red.
      if (!$status) {
        return self::$CODE_RED;
      }

      // Final status uses green for success
      if ($final) {
        return self::$CODE_GREEN;
      }

      // Intermediate status is blue
      return self::$CODE_BLUE;
    }

    /**
     * Recyclce given line
     *
     */
    private function recycleLine() {
      $this->out->write(str_repeat("\x8", $this->len));
      $this->len= 0;
    }

    /**
     * Write test failure
     *
     * @param   unittest.TestOutcome result
     */
    private function writeFailure(TestOutcome $result) {
      $this->recycleLine();
      $this->out->writeLine($result);
      $this->out->writeLine('');
    }

    /**
     * Write colored line
     *
     * @param   string line
     * @param   string code
     */
    private function writeColoredLine($line, $code) {
      $this->len= strlen($line);
      $this->out->write($code.$line.self::$CODE_RESET);
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
      $this->status= FALSE;
      $this->stats['failed']++;
      $this->writeFailure($failure);
    }

    /**
     * Called when a test errors.
     *
     * @param   unittest.TestError error
     */
    public function testError(TestError $error) {
      $this->status= FALSE;
      $this->stats['errored']++;
      $this->writeFailure($error);
    }

    /**
     * Called when a test raises warnings.
     *
     * @param   unittest.TestWarning warning
     */
    public function testWarning(TestWarning $warning) {
      $this->writeFailure($warning);
      $this->stats['warned']++;
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
      $this->stats['skipped']++;
    }

    /**
     * Called when a test is not run because it has been ignored by using
     * the @ignore annotation.
     *
     * @param   unittest.TestSkipped ignore
     */
    public function testNotRun(TestSkipped $ignore) {
      $this->stats['notrun']++;
    }

    /**
     * Called when a test run starts.
     *
     * @param   unittest.TestSuite suite
     */
    public function testRunStarted(TestSuite $suite) {
      $this->sum= $suite->numTests();
      $this->cur= 0;
      $this->stats= array(
        'failed'  => 0,
        'errored' => 0,
        'warned'  => 0,
        'skipped' => 0,
        'notrun'  => 0
      );
      $this->status= TRUE;
    }

    /**
     * Called when a test run finishes.
     *
     * @param   unittest.TestSuite suite
     * @param   unittest.TestResult result
     */
    public function testRunFinished(TestSuite $suite, TestResult $result) {
      $this->writeStatus();
      $this->out->writeLine();

      // Summary output
      $this->out->writeLinef(
        "\n%s: %d/%d run (%d skipped), %d succeeded, %d failed",
        $result->failureCount() > 0 ? 'FAIL' : 'OK',
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
      $this->out->writeLinef(
        'Time taken: %.3f seconds',
        $result->elapsed()
      );
    }
  }
?>
