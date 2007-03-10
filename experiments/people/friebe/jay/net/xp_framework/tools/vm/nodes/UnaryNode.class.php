<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * Unary
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class UnaryNode extends VNode {
    var
      $expression,
      $op;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed expression
     * @param   mixed op
     */
    function __construct($expression, $op) {
      $this->expression= $expression;
      $this->op= $op;
    }  
  }
?>
