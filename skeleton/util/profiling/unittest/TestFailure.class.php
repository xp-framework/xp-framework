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
      $reason   = NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   &lang.Exception reason
     */
    function __construct(&$reason) {
      $this->reason= &$reason;
      parent::__construct();
    }

    /**
     * Return a string representation of this class
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return $this->getClassName().', reason '.(is_a($this->reason, 'Object') 
        ? $this->reason->toString() 
        : var_export($this->reason, 1)
      );
    }
  }
?>
