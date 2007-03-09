<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * DoubleNumber
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class DoubleNumberNode extends VNode {
    public
      $value;
      
    /**
     * Constructor
     *
     * @param   mixed value
     */
    public function __construct($value) {
      $this->value= $value;
    }  
  }
?>
