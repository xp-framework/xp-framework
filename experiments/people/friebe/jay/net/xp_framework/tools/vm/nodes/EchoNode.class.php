<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * Echo
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class EchoNode extends VNode {
    var
      $args;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed[] args
     */
    function __construct($args) {
      $this->args= $args;
    }  
  }
?>
