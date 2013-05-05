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
      return sprintf(
        "%s(test= %s, time= %.3f seconds) {\n  %s\n }",
        $this->getClassName(),
        $this->test->getName(TRUE),
        $this->elapsed,
        xp::stringOf($this->reason, '  ')
      );
    }
  }
?>
