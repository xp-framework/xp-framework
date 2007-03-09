<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * EnumMember
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class EnumMemberNode extends VNode {
    public
      $arg0,
      $arg1;
      
    /**
     * Constructor
     *
     * @param   mixed arg0
     * @param   mixed arg1
     */
    public function __construct($arg0, $arg1) {
      $this->arg0= $arg0;
      $this->arg1= $arg1;
    }  
  }
?>
