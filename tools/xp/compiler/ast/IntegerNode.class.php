<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.ast.NaturalNode');

  /**
   * Represents an integer literal
   *
   * @see   xp://xp.compiler.ast.NaturalNode
   */
  class IntegerNode extends NaturalNode {

    /**
     * Resolve this node's value.
     *
     * @return  var
     */
    public function resolve() {
      return (int)$this->value;
    }
  }
?>
