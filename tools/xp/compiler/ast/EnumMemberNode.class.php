<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.ast.TypeMemberNode');

  /**
   * Represents an enum member
   *
   */
  class EnumMemberNode extends TypeMemberNode {
    public $value = NULL;
    public $body  = NULL;
    
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
