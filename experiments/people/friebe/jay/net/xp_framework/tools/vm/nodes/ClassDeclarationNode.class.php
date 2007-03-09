<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * ClassDeclaration
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class ClassDeclarationNode extends VNode {
    public
      $name, 
      $extends, 
      $interfaces, 
      $statements, 
      $modifiers, 
      $annotations;
      
    /**
     * Constructor
     *
     * @param   string name
     * @param   mixed extends
     * @param   mixed[] interfaces
     * @param   mixed[] statements
     * @param   mixed[] modifiers
     * @param   mixed annotations
     */
    public function __construct($name, $extends, $interfaces, $statements, $modifiers, $annotations) {
      $this->name= $name;
      $this->extends= $extends;
      $this->interfaces= (array)$interfaces;
      $this->statements= $statements;
      $this->modifiers= $modifiers;
      $this->annotations= (array)$annotations;
    }  
  }
?>
