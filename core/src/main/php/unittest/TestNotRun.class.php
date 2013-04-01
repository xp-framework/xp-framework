<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestSkipped');

  /**
   * Indicates a test was ignored
   *
   * @see      xp://unittest.TestSkipped
   */
  class TestNotRun extends Object implements TestSkipped {
    public
      $reason   = '',
      $test     = NULL;
      
    /**
     * Constructor
     *
     * @param   unittest.TestCase test
     * @param   string reason
     */
    public function __construct(TestCase $test, $reason) {
      $this->test= $test;
      $this->reason= $reason;
    }

    /**
     * Returns elapsed time
     *
     * @return  float
     */
    public function elapsed() {
      return 0.0;
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
