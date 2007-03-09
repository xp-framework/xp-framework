<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * MethodCall
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class MethodCallNode extends VNode {
    public
      $class,
      $method,
      $arguments,
      $chain;
      
    /**
     * Constructor
     *
     * @param   mixed class
     * @param   mixed method
     * @param   mixed arguments
     * @param   mixed chain
     */
    public function __construct($class, $method, $arguments, $chain) {
      $this->class= $class;
      $this->method= $method;
      $this->arguments= $arguments;
      $this->chain= $chain;
    }  
  }
?>
