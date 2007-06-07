<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * StaticInitializer
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class StaticInitializerNode extends VNode {
    public
      $block;
      
    /**
     * Constructor
     *
     * @param   mixed block
     */
    public function __construct($block) {
      $this->block= $block;
    }  
  }
?>
