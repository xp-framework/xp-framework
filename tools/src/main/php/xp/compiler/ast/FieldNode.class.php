<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.compiler.ast.TypeMemberNode');

  /**
   * Represents a field
   *
   */
  class FieldNode extends TypeMemberNode {
    public $type           = NULL;
    public $initialization = NULL;

    /**
     * Returns this members's hashcode
     *
     * @return  string
     */
    public function hashCode() {
      return '$'.$this->getName();
    }
  }
?>
