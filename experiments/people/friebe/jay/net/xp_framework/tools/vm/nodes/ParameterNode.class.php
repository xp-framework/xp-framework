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
    public
      $name,
      $type,
      $default,
      $vararg;
      
    /**
     * Constructor
     *
     * @param   string name
     * @param   string type
     * @param   mixed default
     * @param   bool vararg default FALSE
     */
    public function __construct($name, $type, $default, $vararg= FALSE) {
      $this->name= $name;
      $this->type= $type;
      $this->default= $default;
      $this->vararg= $vararg;
    }  
  }
?>
