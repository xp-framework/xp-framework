<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * (Insert class' description here)
   *
   * @ext      extensiom
   * @see      reference
   * @purpose  purpose
   */
  class TestFailure extends Object {
    var
      $reason   = NULL;
      
    function __construct(&$reason) {
      $this->reason= &$reason;
      parent::__construct();
    }
  }
?>
