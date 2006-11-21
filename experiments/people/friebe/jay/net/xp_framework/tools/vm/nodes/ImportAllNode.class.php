<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * Import
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class ImportAllNode extends VNode {
    var
      $package;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed package
     */
    function __construct($package) {
      $this->package= $package;
    }  
  }
?>
