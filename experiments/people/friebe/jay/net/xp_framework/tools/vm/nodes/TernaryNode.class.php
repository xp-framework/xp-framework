<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * Ternary
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class TernaryNode extends VNode {
    public
      $condition,
      $expression,
      $conditional;
      
    /**
     * Constructor
     *
     * @param   mixed condition
     * @param   mixed expression
     * @param   mixed conditional
     */
    public function __construct($condition, $expression, $conditional) {
      $this->condition= $condition;
      $this->expression= $expression;
      $this->conditional= $conditional;
    }  
  }
?>
