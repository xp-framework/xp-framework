<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.ast.Node');

  /**
   * (Insert class' description here)
   *
   */
  class LambdaNode extends xp·compiler·ast·Node {
    public $parameters;
    public $statements;
    
    /**
     * Constructor
     *
     * @param   xp.compiler.ast.Node[] parameters
     * @param   xp.compiler.ast.Node[] statements
     */
    public function __construct(array $parameters, array $statements) {
      $this->parameters= $parameters;
      $this->statements= $statements;
    }
  }
?>
