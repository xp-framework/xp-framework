<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * ObjectReference
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class ObjectReferenceNode extends VNode {
    public
      $class,
      $member,
      $chain;
      
    /**
     * Constructor
     *
     * @param   mixed class
     * @param   mixed member
     * @param   mixed chain
     */
    public function __construct($class, $member, $chain) {
      $this->class= $class;
      $this->member= $member;
      $this->chain= $chain;
    }  
  }
?>
