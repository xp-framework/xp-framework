<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * InvokeableDeclaration
   *
   * @purpose  Base class for Method / Constructor / Destructor / Operator
   * @see      xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class InvokeableDeclarationNode extends VNode {
    var
      $name,
      $parameters,
      $return,
      $statements,
      $modifiers,
      $annotations,
      $throws;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string name 
     * @param   mixed parameters
     * @param   mixed return
     * @param   mixed statements
     * @param   mixed modifiers
     * @param   mixed annotations
     * @param   mixed throws
     */
    function __construct($name, $parameters, $return, $statements, $modifiers, $annotations, $throws) {
      $this->name= $name;
      $this->parameters= $parameters;
      $this->return= $return;
      $this->statements= $statements;
      $this->modifiers= $modifiers;
      $this->annotations= $annotations;
      $this->throws= $throws;
    }  
  }
?>
