<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * Parameter
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class ParameterNode extends VNode {
    var
      $name,
      $type,
      $default;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed name
     * @param   mixed type
     * @param   mixed default
     */
    function __construct($name, $type, $default) {
      $this->name= $name;
      $this->type= $type;
      $this->default= $default;
    }  
  }
?>
