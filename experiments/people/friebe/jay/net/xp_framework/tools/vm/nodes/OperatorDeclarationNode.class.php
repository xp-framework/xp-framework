<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * OperatorDeclaration
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class OperatorDeclarationNode extends VNode {
    var
      $arg0,
      $arg1,
      $arg2,
      $arg3,
      $arg4,
      $arg5,
      $arg6;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed arg0
     * @param   mixed arg1
     * @param   mixed arg2
     * @param   mixed arg3
     * @param   mixed arg4
     * @param   mixed arg5
     * @param   mixed arg6
     */
    function __construct($arg0, $arg1, $arg2, $arg3, $arg4, $arg5, $arg6) {
      $this->arg0= $arg0;
      $this->arg1= $arg1;
      $this->arg2= $arg2;
      $this->arg3= $arg3;
      $this->arg4= $arg4;
      $this->arg5= $arg5;
      $this->arg6= $arg6;
    }  
  }
?>