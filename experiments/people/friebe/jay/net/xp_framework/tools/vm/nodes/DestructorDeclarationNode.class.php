<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.InvokeableDeclarationNode');

  /**
   * DestructorDeclaration
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class DestructorDeclarationNode extends InvokeableDeclarationNode {
      
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed statements
     * @param   mixed modifiers
     * @param   mixed annotations
     * @param   mixed throws
     */
    function __construct($statements, $modifiers, $annotations, $throws) {
      parent::__construct(
        '__destruct', 
        array(), 
        NULL, 
        $statements, 
        $modifiers, 
        $annotations, 
        $throws
      );
    }  
  }
?>
