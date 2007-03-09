<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * MemberDeclaration
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class MemberDeclarationNode extends VNode {
    public
      $name,
      $initial;
      
    /**
     * Constructor
     *
     * @param   mixed name
     * @param   mixed initial
     */
    public function __construct($name, $initial) {
      $this->name= $name;
      $this->initial= $initial;
    }  
  }
?>
