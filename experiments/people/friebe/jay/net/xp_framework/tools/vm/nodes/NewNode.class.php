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
      $instanciation;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed class
     * @param   net.xp_framework.tools.vm.NewClassNode instanciation
     */
    function __construct($class, $instanciation) {
      $this->class= $class;
      $this->instanciation= $instanciation;
    }  
  }
?>
