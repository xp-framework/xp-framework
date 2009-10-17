<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestFailure');

  /**
   * Indicates a test failed
   *
   * @see      xp://unittest.TestFailure
   */
  class TestWarning extends Object implements TestFailure {
    public
      $reason   = NULL,
      $test     = NULL,
      $elapsed  = 0.0;
      
    /**
     * Constructor
     *
     * @param   unittest.TestCase test
     * @param   string[] warnings
     * @param   float elapsed
     */
    public function __construct(TestCase $test, array $warnings, $elapsed) {
      $this->test= $test;
      $this->reason= $warnings;
      $this->elapsed= $elapsed;
    }

    /**
     * Returns elapsed time
     *
     * @return  float
     */
    public function elapsed() {
      return $this->elapsed;
    }

    /**
     * Return a string representation of this class
     *
     * @return  string
     */
    public function toString() {
      return (
        $this->getClassName().
        '(test= '.$this->test->getClassName().'::'.$this->test->getName().
        sprintf(', time= %.3f seconds', $this->elapsed).") {\n  ".
        str_replace("\n", "\n  ", xp::stringOf($this->reason))."\n".
        ' }'
      );
    }
  }
?>
