<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.ast.ConstantValueNode');

  /**
   * Represents a string literal
   *
   */
  class StringNode extends ConstantValueNode {

    /**
     * Returns a hashcode
     *
     * @return  string
     */
    public function hashCode() {
      return 'xp.string:'.$this->value;
    }

    /**
     * Resolve this node's value.
     *
     * @return  var
     */
    public function resolve() {
      return (string)$this->value;
    }
  }
?>
