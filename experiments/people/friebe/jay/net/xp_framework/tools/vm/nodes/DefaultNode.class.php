<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * Default
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class DefaultNode extends VNode {
    public
      $expression,
      $statements;
      
    /**
     * Constructor
     *
     * @param   mixed statements
     */
    public function __construct($statements) {
      $this->statements= $statements;
    }  
  }
?>
