<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.InvokeableDeclarationNode');

  /**
   * ConstructorDeclaration
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class ConstructorDeclarationNode extends InvokeableDeclarationNode {
      
    /**
     * Constructor
     *
     * @param   mixed parameters
     * @param   mixed statements
     * @param   mixed modifiers
     * @param   mixed annotations
     * @param   mixed throws
     */
    public function __construct($parameters, $statements, $modifiers, $annotations, $throws) {
      parent::__construct(
        '__construct', 
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
