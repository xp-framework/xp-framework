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
    var
      $name, 
      $extends, 
      $interfaces, 
      $statements, 
      $modifiers, 
      $annotations;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     * @param   mixed extends
     * @param   mixed[] interfaces
     * @param   mixed[] statements
     * @param   mixed[] modifiers
     * @param   mixed annotations
     */
    function __construct($name, $extends, $interfaces, $statements, $modifiers, $annotations) {
      $this->name= $name;
      $this->extends= $extends;
      $this->interfaces= $interfaces;
      $this->statements= $statements;
      $this->modifiers= $modifiers;
      $this->annotations= $annotations;
    }  
  }
?>
