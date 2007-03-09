<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * Assign
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class AssignNode extends VNode {
    public
      $variable,
      $expression;
      
    /**
     * Constructor
     *
     * @param   mixed variable
     * @param   mixed expression
     */
    public function __construct($variable, $expression) {
      $this->variable= $variable;
      $this->expression= $expression;
    }  
  }
?>
