<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.compiler.ast.Node');

  /**
   * Silence operator
   *
   * Example
   * ~~~~~~~
   * <code>
   *   @$a[0];    // Get array element, suppress warning if !isset
   * </code>
   *
   * Note
   * ~~~~
   * This is only available in PHP syntax!
   *
   */
  class SilenceOperatorNode extends xp·compiler·ast·Node {
    public $expression = NULL;
    
    /**
     * Creates a new dynamic variable reference
     *
     * @param  xp.compiler.ast.Node expression
     */
    public function __construct(xp·compiler·ast·Node $expression) {
      $this->expression= $expression;
    }
  }
?>
