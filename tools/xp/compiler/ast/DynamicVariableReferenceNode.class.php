<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.compiler.ast.Node');

  /**
   * Dynamic variable reference
   *
   * Example
   * ~~~~~~~
   * <code>
   *   $this->$name;
   *   $this->{$name};
   *   $this->{substr($name, 0, -5)};
   * </code>
   *
   * Note
   * ~~~~
   * This is only available in PHP syntax!
   *
   */
  class DynamicVariableReferenceNode extends xp·compiler·ast·Node {
    public $target= NULL;
    public $expression = NULL;
    
    /**
     * Creates a new dynamic variable reference
     *
     * @param   xp.compiler.ast.Node target
     * @param  xp.compiler.ast.Node expression
     */
    public function __construct($target= NULL, xp·compiler·ast·Node $expression) {
      $this->target= $target;
      $this->expression= $expression;
    }
  }
?>
