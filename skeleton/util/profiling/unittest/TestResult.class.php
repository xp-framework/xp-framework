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
    var
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
    function setSucceeded(&$test, $value) {
      $this->succeeded[$test->getClassName().'::'.$test->getName()]= &new TestSuccess($value);
    }
    
    /**
     * Mark a test as failed
     *
     * @access  public
     * @param   &util.profiling.unittest.TestCase test
     * @param   mixed reason
     */
    function setFailed(&$test, $reason) {
      $this->failed[$test->getClassName().'::'.$test->getName()]= &new TestFailure($reason);
    }

    /**
     * Mark a test as been skipped
     *
     * @access  public
     * @param   &util.profiling.unittest.TestCase test
     * @param   mixed reason
     */
    function setSkipped(&$test, $reason) {
      $this->skipped[$test->getClassName().'::'.$test->getName()]= &new TestSkipped($reason);
    }
    
    /**
     * Create a nice string representation
     *
     * @access  public
     * @return  string
     */
    function toString() {
      $div= str_repeat('=', 72);
      $str= sprintf(
        "Results for test suite run at %s\n".
        "%d tests, %d succeeded, %d failed, %d skipped\n",
        date('r'),
        sizeof($this->succeeded)+ sizeof($this->failed)+ sizeof($this->skipped),
        sizeof($this->succeeded),
        sizeof($this->failed),
        sizeof($this->skipped)
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
        $str.= '  * '.$key."\n    returned: ".$this->succeeded[$key]->toString()."\n";
      }
      $str.= "\n- Failed tests details:\n";
      foreach (array_keys($this->failed) as $key) {
        $str.= '  * '.$key."\n    reason: ".$this->failed[$key]->toString()."\n";
      }
      $str.= "\n- Skipped tests details:\n";
      foreach (array_keys($this->skipped) as $key) {
        $str.= '  * '.$key."\n    reason: ".$this->skipped[$key]->toString()."\n";
      }
      
      return $str.$div."\n";
    }
  }
?>
