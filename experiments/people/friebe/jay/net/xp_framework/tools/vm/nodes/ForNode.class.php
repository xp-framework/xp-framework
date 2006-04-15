<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * For
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class ForNode extends VNode {
    var
      $init,
      $condition,
      $loop,
      $statements;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed init
     * @param   mixed condition
     * @param   mixed loop
     * @param   mixed statements
     */
    function __construct($init, $condition, $loop, $statements) {
      $this->init= $init;
      $this->condition= $condition;
      $this->loop= $loop;
      $this->statements= $statements;
    }  
  }
?>
