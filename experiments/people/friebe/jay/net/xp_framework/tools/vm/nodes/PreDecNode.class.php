<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * PreDec (--$i)
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class PreDecNode extends VNode {
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
