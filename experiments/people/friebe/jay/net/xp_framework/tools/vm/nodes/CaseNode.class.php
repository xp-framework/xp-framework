<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * Case
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class CaseNode extends VNode {
    public
      $expression,
      $statements;
      
    /**
     * Constructor
     *
     * @param   mixed expression
     * @param   mixed statements
     */
    public function __construct($expression, $statements) {
      $this->expression= $expression;
      $this->statements= $statements;
    }  
  }
?>
