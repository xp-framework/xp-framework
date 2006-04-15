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
    var
      $name, 
      $statements, 
      $annotations;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     * @param   mixed[] statements
     * @param   mixed annotations
     */
    function __construct($name, $statements, $annotations) {
      $this->name= $name;
      $this->statements= $statements;
      $this->annotations= $annotations;
    }  
  }
?>
