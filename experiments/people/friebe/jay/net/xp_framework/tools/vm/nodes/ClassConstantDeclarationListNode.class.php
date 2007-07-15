<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * ClassConstantDeclarationList
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class ClassConstantDeclarationListNode extends VNode {
    public
      $list;
      
    /**
     * Constructor
     *
     * @param   mixed list
     */
    public function __construct($list) {
      $this->list= $list;
    }  
  }
?>
