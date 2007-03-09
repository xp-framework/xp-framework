<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * ConstantReference
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class ConstantReferenceNode extends VNode {
    public
      $class,
      $name;
      
    /**
     * Constructor
     *
     * @param   string class
     * @param   string name
     */
    public function __construct($class, $name) {
      $this->class= $class;
      $this->name= $name;
    }  
  }
?>
