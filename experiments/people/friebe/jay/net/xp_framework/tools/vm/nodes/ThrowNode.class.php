<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * Throw
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class ThrowNode extends VNode {
    var
      $value;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed value
     */
    function __construct($value) {
      $this->value= $value;
    }  
  }
?>
