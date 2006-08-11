<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * ExpressionCast "(" type ")" expression
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class ExpressionCastNode extends VNode {
    var
      $expression,
      $type;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed expression
     * @param   mixed type
     */
    function __construct($expression, $type) {
      $this->expression= $expression;
      $this->type= $type;
    }  
  }
?>
