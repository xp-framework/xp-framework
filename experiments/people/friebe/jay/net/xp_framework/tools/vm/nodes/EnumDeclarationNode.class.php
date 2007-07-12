<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * EnumDeclaration
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class EnumDeclarationNode extends VNode {
    public
      $name, 
      $members,
      $statements, 
      $annotations;
      
    /**
     * Constructor
     *
     * @param   string name
     * @param   mixed[] members
     * @param   mixed annotations
     * @param   mixed[] statements
     */
    public function __construct($name, $members, $annotations, $statements) {
      $this->name= $name;
      $this->members= $members;
      $this->annotations= $annotations;
      $this->statements= $statements;
    }  
  }
?>
