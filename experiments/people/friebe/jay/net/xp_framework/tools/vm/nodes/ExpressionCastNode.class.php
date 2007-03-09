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
    public
      $expression,
      $type;
      
    /**
     * Constructor
     *
     * @param   mixed expression
     * @param   mixed type
     */
    public function __construct($expression, $type) {
      $this->expression= $expression;
      $this->type= $type;
    }  
  }
?>
