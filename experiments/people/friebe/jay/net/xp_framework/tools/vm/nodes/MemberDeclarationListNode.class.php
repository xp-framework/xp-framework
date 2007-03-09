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
    public
      $modifiers,
      $members,
      $type;
      
    /**
     * Constructor
     *
     * @param   int modifiers
     * @param   string type
     * @param   net.xp_framework.tools.vm.nodes.MemberDeclarationNode[] members
     */
    public function __construct($modifiers, $type, $members) {
      $this->modifiers= $modifiers;
      $this->type= $type;
      $this->members= $members;
    }  
  }
?>
