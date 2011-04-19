<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.compiler.ast.Node');

  /**
   * Represents a while-statement
   *
   * <code>
   *   while (...) {
   *     ...
   *   }
   * </code>
   */
  class WhileNode extends xp·compiler·ast·Node {
    public $statements= NULL;
    public $expression= NULL;

    /**
     * Constructor
     *
     * @param   xp.compiler.ast.Node expression
     * @param   xp.compiler.ast.Node[] statements
     */
    public function __construct(xp·compiler·ast·Node $expression= NULL, $statements= array()) {
      $this->expression= $expression;
      $this->statements= $statements;
    }
  }
?>
