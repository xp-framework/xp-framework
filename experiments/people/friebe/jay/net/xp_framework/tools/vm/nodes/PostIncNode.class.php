<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * PostInc
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class PostIncNode extends VNode {
    var
      $arg0;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed arg0
     */
    function __construct($arg0) {
      $this->arg0= $arg0;
    }  
  }
?>