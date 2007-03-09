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
  class ArrayOffsetNode extends VNode {
    public
      $expression;
      
    /**
     * Constructor
     *
     * @param   mixed expression
     */
    public function __construct($expression) {
      $this->expression= $expression;
    }  
  }
?>
