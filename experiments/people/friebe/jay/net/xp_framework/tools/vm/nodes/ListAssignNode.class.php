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
    var
      $assignments,
      $expression;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed assignments
     * @param   mixed expression
     */
    function __construct($assignments, $expression) {
      $this->assignments= $assignments;
      $this->expression= $expression;
    }  
  }
?>
