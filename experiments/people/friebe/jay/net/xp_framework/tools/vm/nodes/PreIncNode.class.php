<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * PreInc (++$i)
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class PreIncNode extends VNode {
    var
      $expression;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed expression
     */
    function __construct($expression) {
      $this->expression= $expression;
    }  
  }
?>
