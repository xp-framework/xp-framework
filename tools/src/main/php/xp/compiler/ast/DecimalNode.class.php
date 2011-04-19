<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.ast.NumberNode');

  /**
   * Represents a decimal literal
   *
   */
  class DecimalNode extends NumberNode {

    /**
     * Resolve this node's value.
     *
     * @return  var
     */
    public function resolve() {
      return (double)$this->value;
    }
  }
?>
