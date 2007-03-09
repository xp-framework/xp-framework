<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * ArrayDeclaration
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class ArrayDeclarationNode extends VNode {
    public
      $elements= array();
      
    /**
     * Constructor
     *
     * @param   mixed[] elements
     */
    public function __construct($elements) {
      $this->elements= $elements;
    }  
  }
?>
