<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * MemberDeclarationList
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class MemberDeclarationListNode extends VNode {
    var
      $modifiers,
      $members;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   int modifiers
     * @param   net.xp_framework.tools.vm.nodes.MemberDeclarationNode[] members
     */
    function __construct($modifiers, $members) {
      $this->modifiers= $modifiers;
      $this->members= $members;
    }  
  }
?>
