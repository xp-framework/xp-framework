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
      $elements = array(),
      $type     = NULL;
      
    /**
     * Constructor
     *
     * @param   mixed[] elements
     * @param   mixed type= NULL
     */
    public function __construct($elements, $type= NULL) {
      $this->elements= $elements;
      $this->type= $type;
    }  
  }
?>
