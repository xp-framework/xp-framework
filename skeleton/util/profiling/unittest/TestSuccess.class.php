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
  class TestSuccess extends Object {
    var
      $result   = NULL;
      
    function __construct(&$result) {
      $this->result= &$result;
      parent::__construct();
    }
  }
?>
