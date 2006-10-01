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
    var
      $elements= array();
      
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed[] elements
     */
    function __construct($elements) {
      $this->elements= $elements;
    }  
  }
?>
