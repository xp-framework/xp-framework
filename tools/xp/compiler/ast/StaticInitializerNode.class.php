<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.ast.TypeMemberNode');

  /**
   * Static initializer
   *
   */
  class StaticInitializerNode extends TypeMemberNode {
    public $statements;
    
    /**
     * Creates a new static initializer node
     *
     * @param   xp.compiler.ast.Node[] statements
     */
    public function __construct($statements) {
      $this->statements= $statements;
    }

    /**
     * Returns this member's name
     *
     * @return  string
     */
    public function getName() {
      return '__static';
    }

    /**
     * Returns this members's hashcode
     *
     * @return  string
     */
    public function hashCode() {
      return '__static()';
    }
  }
?>
