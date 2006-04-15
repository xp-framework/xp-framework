<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * FunctionCall
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class FunctionCallNode extends VNode {
    var
      $name,
      $arguments;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed name
     * @param   mixed arguments
     */
    function __construct($name, $arguments) {
      $this->name= $name;
      $this->arguments= $arguments;
    }  
  }
?>
