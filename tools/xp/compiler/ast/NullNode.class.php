<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.ast.ConstantValueNode');

  /**
   * Represents the null literal
   *
   */
  class NullNode extends ConstantValueNode {

    /**
     * Creates a new constant value node with a given value
     *
     * @param   string value
     */
    public function __construct($value= NULL) {
      parent::__construct(NULL);
    }

    /**
     * Returns a hashcode
     *
     * @return  string
     */
    public function hashCode() {
      return 'xp.null';
    }

    /**
     * Resolve this node's value.
     *
     * @return  var
     */
    public function resolve() {
      return NULL;
    }
  }
?>
