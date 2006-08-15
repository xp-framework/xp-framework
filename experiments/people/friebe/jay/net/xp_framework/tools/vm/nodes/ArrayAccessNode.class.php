<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * ArrayOffset
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class ArrayAccessNode extends VNode {
    var
      $expression,
      $offset;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed expression
     * @param   mixed offset
     */
    function __construct($expression, $offset) {
      $this->expression= $expression;
      $this->offset= $offset;
    }  
  }
?>
