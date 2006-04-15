<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * New
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class NewNode extends VNode {
    var
      $class,
      $arguments;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed class
     * @param   mixed arguments
     */
    function __construct($class, $arguments) {
      $this->class= $class;
      $this->arguments= $arguments;
    }  
  }
?>
