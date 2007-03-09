<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * NewClass
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class NewClassNode extends VNode {
    public
      $arguments,
      $declaration,
      $chain;
      
    /**
     * Constructor
     *
     * @param   mixed arguments
     * @param   mixed declaration
     * @param   mixed chain
     */
    public function __construct($arguments, $declaration, $chain) {
      $this->arguments= $arguments;
      $this->declaration= $declaration;
      $this->chain= $chain;
    }  
  }
?>
