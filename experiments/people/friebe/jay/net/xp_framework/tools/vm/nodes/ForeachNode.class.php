<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * Foreach
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class ForeachNode extends VNode {
    var
      $expression,
      $key,
      $value,
      $statements;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed expression
     * @param   mixed key
     * @param   mixed value
     * @param   mixed statements
     */
    function __construct($expression, $key, $value, $statements) {
      $this->expression= $expression;
      $this->key= $key;
      $this->value= $value;
      $this->statements= $statements;
    }  
  }
?>
