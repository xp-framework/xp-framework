<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * Boolean
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class BooleanOperatorNode extends VNode {
    public
      $left,
      $right,
      $op;
      
    /**
     * Constructor
     *
     * @param   mixed left
     * @param   mixed right
     * @param   mixed op
     */
    public function __construct($left, $right, $op) {
      $this->left= $left;
      $this->right= $right;
      $this->op= $op;
    }  
  }
?>
