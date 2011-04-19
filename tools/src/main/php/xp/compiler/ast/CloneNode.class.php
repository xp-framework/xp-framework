<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.ast.Node');

  /**
   * Represents the clone statement
   *
   * <code>
   *   $clone= clone $expression;
   * </code>
   *
   */
  class CloneNode extends xp·compiler·ast·Node {
    public $expression;
    
    /**
     * Constructor
     *
     * @param   xp.compiler.ast.Node expression
     */
    public function __construct(xp·compiler·ast·Node $expression) {
      $this->expression= $expression;
    }
  }
?>
