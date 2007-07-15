<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * ClassConstantDeclaration
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class ClassConstantDeclarationNode extends VNode {
    public
      $name,
      $value;
      
    /**
     * Constructor
     *
     * @param   mixed name
     * @param   mixed value
     */
    public function __construct($name, $value) {
      $this->name= $name;
      $this->value= $value;
    }  
  }
?>
