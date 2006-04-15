<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * InterfaceDeclaration
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class InterfaceDeclarationNode extends VNode {
    var
      $name, 
      $extends, 
      $statements, 
      $modifiers, 
      $annotations;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed name
     * @param   mixed[] extends
     * @param   mixed statements
     * @param   mixed modifiers
     * @param   mixed annotations
     */
    function __construct($name, $extends, $statements, $modifiers, $annotations) {
      $this->name= $name;
      $this->extends= $extends;
      $this->statements= $statements;
      $this->modifiers= $modifiers;
      $this->annotations= $annotations;
    }  
  }
?>
