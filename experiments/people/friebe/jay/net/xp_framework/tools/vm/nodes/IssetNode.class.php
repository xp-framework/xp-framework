<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * Isset
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class IssetNode extends VNode {
    var
      $list;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed[] list
     */
    function __construct($list) {
      $this->list= $list;
    }  
  }
?>
