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
    public
      $class,
      $variable,
      $statements,
      $catches;
      
    /**
     * Constructor
     *
     * @param   mixed class
     * @param   mixed variable
     * @param   mixed statements
     * @param   mixed catches
     */
    public function __construct($class, $variable, $statements, $catches) {
      $this->class= $class;
      $this->variable= $variable;
      $this->statements= $statements;
      $this->catches= $catches;
    }  
  }
?>
