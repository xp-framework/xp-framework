<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.ast.Node');

  /**
   * Represents a braced expression:
   *
   * <code>
   *   ( 3 + 2 ) * 5
   * </code>
   * 
   * Braces are used for precedence.
   */
  class BracedExpressionNode extends xp·compiler·ast·Node {
    public $expression= NULL;
    
    /**
     * Constructor
     *
     * @param   xp.compiler.ast.Node expression
     */
    public function __construct(xp·compiler·ast·Node $expression) {
      $this->expression= $expression;
    }
    
    /**
     * Returns a hashcode
     *
     * @return  string
     */
    public function hashCode() {
      return '('.$this->expression->hashCode().')';
    }
  }
?>
