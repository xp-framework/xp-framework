<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * ListAssign
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class ListAssignNode extends VNode {
    public
      $assignments,
      $expression;
      
    /**
     * Constructor
     *
     * @param   mixed assignments
     * @param   mixed expression
     */
    public function __construct($assignments, $expression) {
      $this->assignments= $assignments;
      $this->expression= $expression;
    }  
  }
?>
