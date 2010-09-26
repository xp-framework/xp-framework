<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.compiler.ast.Node');

  /**
   * Represents a return statement.
   *
   * <code>
   *   return;
   *   return $a;
   * </code>
   */
  class ReturnNode extends xp·compiler·ast·Node {
    public $expression= NULL;
    
    /**
     * Constructor
     *
     * @param   xp.compiler.ast.Node expression
     */
    public function __construct(xp·compiler·ast·Node $expression= NULL) {
      $this->expression= $expression;
    }
  }
?>
