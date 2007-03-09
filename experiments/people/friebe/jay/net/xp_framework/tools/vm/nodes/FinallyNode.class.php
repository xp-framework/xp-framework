<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * Finally
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class FinallyNode extends VNode {
    public
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
