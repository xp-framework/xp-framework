<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Indicates a test was skipped
   *
   * @see      xp://util.profiling.unittest.TestResult
   * @purpose  Result wrapper
   */
  class TestSkipped extends Object {
    var
      $reason   = NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   &mixed reason
     */
    function __construct(&$reason) {
      $this->reason= &$reason;
      parent::__construct();
    }
  }
?>
