<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Indicates a test was successful
   *
   * @see      xp://util.profiling.unittest.TestResult
   * @purpose  Result wrapper
   */
  class TestSuccess extends Object {
    var
      $result   = NULL,
      $test     = NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   &util.profiling.unittest.TestCase test
     * @param   &mixed result
     */
    function __construct(&$test, &$result) {
      $this->test= &$test;
      $this->result= &$result;
      
    }
    
    /**
     * Return a string representation of this class
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return $this->getClassName().', result '.(is_a($this->result, 'Object') 
        ? $this->result->toString() 
        : var_export($this->result, 1)
      );
    }
  }
?>
