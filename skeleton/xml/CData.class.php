<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  class CData extends Object {
    var
      $cdata= '';
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string cdata
     */
    function __construct($cdata) {
      $this->cdata= $cdata;
      parent::__construct();
    }
  }
?>
