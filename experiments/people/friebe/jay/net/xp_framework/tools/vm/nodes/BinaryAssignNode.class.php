<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * BinaryAssign
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class BinaryAssignNode extends VNode {
    public
      $variable,
      $expression,
      $op;
      
    /**
     * Constructor
     *
     * @param   mixed variable
     * @param   mixed expression
     * @param   string op
     */
    public function __construct($variable, $expression, $op) {
      $this->variable= $variable;
      $this->expression= $expression;
      $this->op= $op;
    }  
  }
?>
