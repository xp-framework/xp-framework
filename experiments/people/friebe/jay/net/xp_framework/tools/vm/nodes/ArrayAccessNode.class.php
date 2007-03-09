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
    public
      $expression,
      $offset;
      
    /**
     * Constructor
     *
     * @param   mixed expression
     * @param   mixed offset
     */
    public function __construct($expression, $offset) {
      $this->expression= $expression;
      $this->offset= $offset;
    }  
  }
?>
