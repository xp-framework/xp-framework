<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * StaticMember
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class StaticMemberNode extends VNode {
    public
      $class,
      $member;
      
    /**
     * Constructor
     *
     * @param   mixed class
     * @param   mixed member
     */
    public function __construct($class, $member) {
      $this->class= $class;
      $this->member= $member;
    }  
  }
?>
