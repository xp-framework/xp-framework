<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.ast.Node', 'xp.compiler.ast.Resolveable');

  /**
   * Represents a constant value
   *
   */
  abstract class ConstantValueNode extends xp·compiler·ast·Node implements Resolveable {
    public $value= NULL;

    /**
     * Creates a new constant value node with a given value
     *
     * @param   string value
     */
    public function __construct($value= NULL) {
      $this->value= $value;
    }


    /**
     * Returns a hashcode
     *
     * @return  string
     */
    public function hashCode() {
      return $this->value;
    }
  }
?>
