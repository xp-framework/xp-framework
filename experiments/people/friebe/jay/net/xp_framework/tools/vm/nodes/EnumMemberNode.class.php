<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * EnumMember
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class EnumMemberNode extends VNode {
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
