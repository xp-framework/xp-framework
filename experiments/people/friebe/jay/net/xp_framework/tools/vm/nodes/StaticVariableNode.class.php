<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * StaticVariable
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class StaticVariableNode extends VNode {
    var
      $name,
      $initial;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     * @param   mixed initial
     */
    function __construct($name, $initial) {
      $this->name= $name;
      $this->initial= $initial;
    }  
  }
?>
