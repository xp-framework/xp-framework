<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * PropertyDeclaration
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class PropertyDeclarationNode extends VNode {
    public
      $name,
      $accessors;
      
    /**
     * Constructor
     *
     * @param   mixed name
     * @param   mixed accessors
     */
    public function __construct($name, $accessors) {
      $this->name= $name;
      $this->accessors= $accessors;
    }  
  }
?>
