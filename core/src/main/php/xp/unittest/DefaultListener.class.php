<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestListener',
    'unittest.ColorizingListener',
    'io.streams.OutputStreamWriter',
    'lang.Runtime'
  );

  /**
   * Default listener
   * ----------------
   * Only shows details for failed tests. This listener has no options.
   */
  class DefaultListener extends Object implements TestListener, ColorizingListener {
    const OUTPUT_WIDTH  = 72;

    public $out= NULL;
    protected $column= 0;
    private $colored= NULL;

    /**
     * Constructor
     *
     * @param   io.streams.OutputStreamWriter out
     */
    public function __construct(OutputStreamWriter $out) {
      $this->out= $out;
    }

    /**
     * Set color
     *
     * @param   bool color
     * @return  self
     */
    public function setColor($color) {
      if (NULL === $color) {
        $color= getenv('TERM') || getenv('ANSICON');
      }

      $this->colored= $color;
    }

    /**
     * Set color
     *
     * @param   bool color
     * @return  self
     */
    public function withColor($color) {
      $this->setColor($color);
      return $this;
    }

    /**
     * Output method; takes care of wrapping output if output line
     * exceeds maximum length
     *
     * @param   string string
     */
    protected function write($string) {
      if ($this->column > self::OUTPUT_WIDTH) {
        $this->out->writeLine();
        $this->column= 0;
      }

      $this->column++;
      $this->out->write($string);
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
      $this->write('F');
    }

    /**
     * Called when a test errors.
     *
     * @param   unittest.TestError error
     */
    public function testError(TestError $error) {
      $this->write('E');
    }

    /**
     * Called when a test raises warnings.
     *
     * @param   unittest.TestWarning warning
     */
    public function testWarning(TestWarning $warning) {
      $this->write('W');
    }
    
    /**
     * Called when a test finished successfully.
     *
     * @param   unittest.TestSuccess success
     */
    public function testSucceeded(TestSuccess $success) {
      $this->write('.');
    }
    
    /**
     * Called when a test is not run because it is skipped due to a 
     * failed prerequisite.
     *
     * @param   unittest.TestSkipped skipped
     */
    public function testSkipped(TestSkipped $skipped) {
      $this->write('S');
    }

    /**
     * Called when a test is not run because it has been ignored by using
     * the @ignore annotation.
     *
     * @param   unittest.TestSkipped ignore
     */
    public function testNotRun(TestSkipped $ignore) {
      $this->write('N');
    }

    /**
     * Called when a test run starts.
     *
     * @param   unittest.TestSuite suite
     */
    public function testRunStarted(TestSuite $suite) {
      $this->write('[');
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
      $fail= FALSE;
      if ($result->failureCount() > 0) {
        $this->out->writeLine();
        foreach ($result->failed as $failure) {
          $this->out->writeLine('F ', $failure);
        }
        $fail= TRUE;
      }

      $this->out->writeLinef(
        "\n%s%s: %d/%d run (%d skipped), %d succeeded, %d failed%s",
        $this->colored ? ($fail ? "\033[41;1;37m" : "\033[42;1;37m") : '',
        $fail ? 'FAIL' : 'OK',
        $result->runCount(),
        $result->count(),
        $result->skipCount(),
        $result->successCount(),
        $result->failureCount(),
        $this->colored ? "\033[0m" : ''
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
