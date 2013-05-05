<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestSkipped');

  /**
   * Indicates a test was skipped
   *
   * @see      xp://unittest.TestSkipped
   */
  class TestPrerequisitesNotMet extends Object implements TestSkipped {
    public
      $reason   = NULL,
      $test     = NULL,
      $elapsed  = 0.0;
      
    /**
     * Constructor
     *
     * @param   unittest.TestCase test
     * @param   unittest.PrerequisitesNotMetError reason
     * @param   float elapsed
     */
    public function __construct(TestCase $test, PrerequisitesNotMetError $reason, $elapsed) {
      $this->test= $test;
      $this->reason= $reason;
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
