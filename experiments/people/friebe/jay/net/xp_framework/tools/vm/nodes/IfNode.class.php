<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * If
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class IfNode extends VNode {
    public
      $condition,
      $statements,
      $elseif,
      $else;
      
    /**
     * Constructor
     *
     * @param   mixed condition
     * @param   mixed statements
     * @param   mixed elseif
     * @param   mixed else
     */
    public function __construct($condition, $statements, $elseif, $else) {
      $this->condition= $condition;
      $this->statements= $statements;
      $this->elseif= $elseif;
      $this->else= $else;
    }  
  }
?>
