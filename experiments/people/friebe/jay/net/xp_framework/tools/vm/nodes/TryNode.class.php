<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * Try
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class TryNode extends VNode {
    var
      $statements,
      $catch,
      $finally;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed $statements
     * @param   mixed $catch
     * @param   mixed $catches
     */
    function __construct($statements, $catch, $finally) {
      $this->statements= $statements;
      $this->catch= $catch;
      $this->finally= $finally;
    }  
  }
?>
