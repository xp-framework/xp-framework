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
    public
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
    public function setSucceeded(&$test, $value) {
      $this->succeeded[$test->hashCode()]= new TestSuccess($test, $value);
    }
    
    /**
     * Mark a test as failed
     *
     * @access  public
     * @param   &util.profiling.unittest.TestCase test
     * @param   mixed reason
     */
    public function setFailed(&$test, $reason) {
      $this->failed[$test->hashCode()]= new TestFailure($test, $reason);
    }

    /**
     * Mark a test as been skipped
     *
     * @access  public
     * @param   &util.profiling.unittest.TestCase test
     * @param   mixed reason
     */
    public function setSkipped(&$test, $reason) {
      $this->skipped[$test->hashCode()]= new TestSkipped($test, $reason);
    }

    /**
     * Get number of succeeded tests
     *
     * @access  public
     * @return  int
     */
    public function successCount() {
      return sizeof($this->succeeded);
    }
    
    /**
     * Get number of failed tests
     *
     * @access  public
     * @return  int
     */
    public function failureCount() {
      return sizeof($this->failed);
    }

    /**
     * Get number of skipped tests
     *
     * @access  public
     * @return  int
     */
    public function skipCount() {
      return sizeof($this->skipped);
    }

    /**
     * Get number of run tests (excluding skipped)
     *
     * @access  public
     * @return  int
     */
    public function runCount() {
      return sizeof($this->succeeded) + sizeof($this->failed);
    }

    /**
     * Get number of total tests
     *
     * @access  public
     * @return  int
     */
    public function count() {
      return sizeof($this->succeeded)+ sizeof($this->failed)+ sizeof($this->skipped);
    }
    
    /**
     * Create a nice string representation
     *
     * @access  public
     * @return  string
     */
    public function toString() {
      $div= str_repeat('=', 72);
      $str= sprintf(
        "Results for test suite run at %s\n".
        "%d tests, %d succeeded, %d failed, %d skipped\n",
        date('r'),
        self::count(),
        self::successCount(),
        self::failureCount(),
        self::skipCount()
      );
      
      // Configuration summary
      $str.= $div."\n".sprintf(
        "Operating system:  %s\n".
        "PHP Version:       %s\n".
        "Zend Version:      %s\n".
        "SAPI:              %s\n".
        "Loaded extensions: %s\n",
        PHP_OS,
        phpversion(),
        zend_version(),
        php_sapi_name(),
        wordwrap(implode(', ', get_loaded_extensions()), 52, "\n                   ")
      ).$div;
      
      // Details
      $str.= "\n- Succeeded tests details:\n";
      foreach (array_keys($this->succeeded) as $key) {
        $str.= sprintf(
          "  * %s::%s\n    returned: %s\n",
          $this->succeeded[$key]->test->getClassName(),
          $this->succeeded[$key]->test->getName(),
          $this->succeeded[$key]->toString()
        );
      }
      $str.= "\n- Failed tests details:\n";
      foreach (array_keys($this->failed) as $key) {
        $str.= sprintf(
          "  * %s::%s\n    returned: %s\n",
          $this->failed[$key]->test->getClassName(),
          $this->failed[$key]->test->getName(),
          $this->failed[$key]->toString()
        );
      }
      $str.= "\n- Skipped tests details:\n";
      foreach (array_keys($this->skipped) as $key) {
        $str.= sprintf(
          "  * %s::%s\n    returned: %s\n",
          $this->skipped[$key]->test->getClassName(),
          $this->skipped[$key]->test->getName(),
          $this->skipped[$key]->toString()
        );
      }
      
      return $str.$div."\n";
    }
  }
?>
