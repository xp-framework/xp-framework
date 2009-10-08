<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestSuccess');

  /**
   * Indicates a test was successful
   *
   * @see      xp://unittest.TestSuccess
   */
  class TestExpectationMet extends Object implements TestSuccess {
    public
      $test     = NULL,
      $elapsed  = 0.0;
      
    /**
     * Constructor
     *
     * @param   unittest.TestCase test
     * @param   float elapsed
     */
    public function __construct(TestCase $test, $elapsed) {
      $this->test= $test;
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
        sprintf(', time= %.3f seconds', $this->elapsed).
        ')'
      );
    }
  }
?>
