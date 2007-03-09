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
    public
      $name,
      $offset;
      
    /**
     * Constructor
     *
     * @param   mixed name
     */
    public function __construct($name) {
      $this->name= $name;
    }
    
    /**
     * Returns a hashcode for this variable
     *
     * @return  string
     */
    public function hashCode() {
      return $this->name;
    }
  }
?>
