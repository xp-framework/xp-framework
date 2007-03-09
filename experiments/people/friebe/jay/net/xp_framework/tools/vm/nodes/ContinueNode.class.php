<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * Continue
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class ContinueNode extends VNode {
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
