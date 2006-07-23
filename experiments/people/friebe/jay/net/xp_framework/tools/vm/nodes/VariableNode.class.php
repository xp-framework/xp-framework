<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * Variable
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class VariableNode extends VNode {
    var
      $name,
      $offset;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed name
     */
    function __construct($name) {
      $this->name= $name;
    }
    
    function hashCode() {
      return $this->name;
    }
  }
?>
