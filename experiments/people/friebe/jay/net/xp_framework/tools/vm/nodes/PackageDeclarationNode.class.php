<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * PackageDeclaration
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class PackageDeclarationNode extends VNode {
    var
      $name,
      $statements;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed name
     * @param   mixed statements
     */
    function __construct($name, $statements) {
      $this->name= $name;
      $this->statements= $statements;
    }  
  }
?>
