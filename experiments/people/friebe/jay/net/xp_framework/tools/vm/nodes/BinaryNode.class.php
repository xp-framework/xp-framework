<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * Binary
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class BinaryNode extends VNode {
    var
      $left,
      $right,
      $op;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed left
     * @param   mixed right
     * @param   string op
     */
    function __construct($left, $right, $op) {
      $this->left= $left;
      $this->right= $right;
      $this->op= $op;
    }  
  }
?>
