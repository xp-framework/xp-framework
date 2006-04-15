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
    var
      $class,
      $member,
      $chain;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed class
     * @param   mixed member
     * @param   mixed chain
     */
    function __construct($class, $member, $chain) {
      $this->class= $class;
      $this->member= $member;
      $this->chain= $chain;
    }  
  }
?>
