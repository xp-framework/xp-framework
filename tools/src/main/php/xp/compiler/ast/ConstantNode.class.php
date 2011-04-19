<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.ast.ConstantValueNode');

  /**
   * Represents a constant
   *
   */
  class ConstantNode extends ConstantValueNode {
  
    /**
     * Returns a hashcode
     *
     * @return  string
     */
    public function hashCode() {
      return $this->value;
    }

    /**
     * Resolve this node's value.
     *
     * @return  var
     */
    public function resolve() {
      if (!defined($this->value)) {
        throw new IllegalStateException('Undefined constant '.$this->value);
      }
      $resolved= constant($this->value);
      if (is_resource($resolved) || is_object($resolved)) {
        throw new IllegalStateException('Constant '.$this->value.' resolves to non-primitive type '.xp::typeOf($resolved));
      }
      return $resolved;
    }
  }
?>
