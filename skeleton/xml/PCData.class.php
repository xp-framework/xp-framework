<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  class PCData extends Object {
    var
      $pcdata= '';
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string pcdata
     */
    function __construct($pcdata) {
      $this->pcdata= $pcdata;
      parent::__construct();
    }
  }
?>
