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
    var
      $condition,
      $expression,
      $conditional;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed condition
     * @param   mixed expression
     * @param   mixed conditional
     */
    function __construct($condition, $expression, $conditional) {
      $this->condition= $condition;
      $this->expression= $expression;
      $this->conditional= $conditional;
    }  
  }
?>
