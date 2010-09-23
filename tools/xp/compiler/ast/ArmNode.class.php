<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.ast.Node');

  /**
   * The "try (...) { block }" statement - Automatic Resource Management
   *
   */
  class ArmNode extends xp·compiler·ast·Node {
    public $initializations;
    public $variables;
    public $statements;
    
    /**
     * Constructor
     *
     * @param   xp.compiler.ast.Node[] declarations
     * @param   xp.compiler.ast.Node[] variables
     * @param   xp.compiler.ast.Node[] statements
     */
    public function __construct(array $initializations, array $variables, array $statements) {
      $this->initializations= $initializations;
      $this->variables= $variables;
      $this->statements= $statements;
    }
  }
?>
