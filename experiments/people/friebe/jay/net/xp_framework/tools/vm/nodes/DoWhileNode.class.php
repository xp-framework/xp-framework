<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * Do ... While
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class DoWhileNode extends VNode {
    public
      $condition,
      $statements;
      
    /**
     * Constructor
     *
     * @param   mixed condition
     * @param   mixed statements
     */
    public function __construct($condition, $statements) {
      $this->condition= $condition;
      $this->statements= $statements;
    }  
  }
?>
