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
      $returns,
      $statements,
      $modifiers,
      $annotations,
      $thrown;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string name 
     * @param   mixed parameters
     * @param   mixed returns
     * @param   mixed statements
     * @param   mixed modifiers
     * @param   mixed annotations
     * @param   mixed thrown
     */
    function __construct($name, $parameters, $returns, $statements, $modifiers, $annotations, $thrown) {
      $this->name= $name;
      $this->parameters= $parameters;
      $this->returns= $returns;
      $this->statements= $statements;
      $this->modifiers= $modifiers;
      $this->annotations= $annotations;
      $this->thrown= $thrown;
    }  
  }
?>
