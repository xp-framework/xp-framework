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
    var
      $condition,
      $statements;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed condition
     * @param   mixed statements
     */
    function __construct($condition, $statements) {
      $this->condition= $condition;
      $this->statements= $statements;
    }  
  }
?>
