<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.InvokeableDeclarationNode');

  /**
   * OperatorDeclaration
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class OperatorDeclarationNode extends InvokeableDeclarationNode {
      
    /**
     * Constructor
     *
     * @param   string name the operator, e.g. "+", "-", etc.
     * @param   mixed parameters
     * @param   mixed statements
     * @param   mixed modifiers
     * @param   mixed annotations
     * @param   mixed throws
     */
    public function __construct($name, $parameters, $statements, $modifiers, $annotations, $throws) {
      parent::__construct(
        $name, 
        $parameters, 
        NULL, 
        $statements, 
        $modifiers, 
        $annotations, 
        $throws
      );
    }  
  }
?>
