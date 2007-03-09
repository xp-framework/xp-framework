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
    public
      $from;
      
    /**
     * Constructor
     *
     * @param   mixed from
     */
    public function __construct($from) {
      $this->from= $from;
    }  
  }
?>
