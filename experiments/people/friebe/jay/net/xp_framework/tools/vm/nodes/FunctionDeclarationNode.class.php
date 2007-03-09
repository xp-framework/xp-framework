<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * FunctionDeclaration
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class FunctionDeclarationNode extends VNode {
    public
      $name,
      $parameters,
      $statements;
      
    /**
     * Constructor
     *
     * @param   string name
     * @param   mixed parameters
     * @param   mixed statements
     */
    public function __construct($name, $parameters, $statements) {
      $this->name= $name;
      $this->parameters= $parameters;
      $this->statements= $statements;
    }  
  }
?>
