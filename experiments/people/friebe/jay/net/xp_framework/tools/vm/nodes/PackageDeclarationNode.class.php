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
    public
      $name,
      $statements;
      
    /**
     * Constructor
     *
     * @param   mixed name
     * @param   mixed statements
     */
    public function __construct($name, $statements) {
      $this->name= $name;
      $this->statements= $statements;
    }  
  }
?>
