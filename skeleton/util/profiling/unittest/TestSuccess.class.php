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
      $result   = NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   &mixed result
     */
    function __construct(&$result) {
      $this->result= &$result;
      parent::__construct();
    }
  }
?>
