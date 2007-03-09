<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * Try
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class TryNode extends VNode {
    public
      $statements,
      $firstCatch,
      $finallyBlock;
      
    /**
     * Constructor
     *
     * @param   mixed statements
     * @param   mixed firstCatch
     * @param   mixed finallyBlock
     */
    public function __construct($statements, $firstCatch, $finallyBlock) {
      $this->statements= $statements;
      $this->firstCatch= $firstCatch;
      $this->finallyBlock= $finallyBlock;
    }  
  }
?>
