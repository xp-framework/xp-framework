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
