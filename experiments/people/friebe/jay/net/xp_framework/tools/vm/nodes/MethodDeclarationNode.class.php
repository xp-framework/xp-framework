<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.InvokeableDeclarationNode');

  /**
   * MethodDeclaration
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class MethodDeclarationNode extends InvokeableDeclarationNode {

    /**
     * Constructor
     *
     * @access  public
     * @param   string name the method's name
     * @param   mixed parameters
     * @param   mixed return
     * @param   mixed statements
     * @param   mixed modifiers
     * @param   mixed annotations
     * @param   mixed throws
     */
    function __construct($name, $parameters, $return, $statements, $modifiers, $annotations, $throws) {
      parent::__construct(
        $name, 
        $parameters, 
        $return, 
        $statements, 
        $modifiers, 
        $annotations, 
        $throws
      );
    }  
  }
?>
