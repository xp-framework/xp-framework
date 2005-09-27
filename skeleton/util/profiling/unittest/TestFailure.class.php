<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Indicates a test failed
   *
   * @see      xp://util.profiling.unittest.TestResult
   * @purpose  Result wrapper
   */
  class TestFailure extends Object {
    var
      $result   = NULL,
      $test     = NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   &util.profiling.unittest.TestCase test
     * @param   &mixed reason
     */
    function __construct(&$test, &$reason) {
      $this->test= &$test;
      $this->reason= &$reason;
      
    }

    /**
     * Return a string representation of this class
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return (
        $this->getClassName().'(test= '.$this->test->getName().") {\n  ".
        str_replace("\n", "\n  ", xp::stringOf($this->reason))."\n".
        ' }'
      );
    }
  }
?>
