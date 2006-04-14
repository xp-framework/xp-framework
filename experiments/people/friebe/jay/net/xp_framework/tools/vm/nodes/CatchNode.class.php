<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * Catch
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class CatchNode extends VNode {
    var
      $arg0,
      $arg1,
      $arg2;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed arg0
     * @param   mixed arg1
     * @param   mixed arg2
     */
    function __construct($arg0, $arg1, $arg2) {
      $this->arg0= $arg0;
      $this->arg1= $arg1;
      $this->arg2= $arg2;
    }  
  }
?>