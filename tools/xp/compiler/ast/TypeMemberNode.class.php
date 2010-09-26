<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.compiler.ast.Node');

  /**
   * Represents a type member
   *
   * @see      xp://xp.compiler.ast.RoutineNode
   * @see      xp://xp.compiler.ast.FieldNode
   * @see      xp://xp.compiler.ast.EnumMemberNode
   */
  abstract class TypeMemberNode extends xp·compiler·ast·Node {
    public $name= '';
    public $modifiers= 0;
    public $annotations= array();

    /**
     * Returns this routine's name
     *
     * @return  string
     */
    public function getName() {
      return $this->name;
    }

    /**
     * Returns this members's hashcode
     *
     * @return  string
     */
    public function hashCode() {
      raise('lang.MethodNotImplementedException', 'Not implemented', __METHOD__);
    }
  }
?>
