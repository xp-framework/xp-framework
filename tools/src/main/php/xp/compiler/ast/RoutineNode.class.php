<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'xp.compiler.ast.TypeMemberNode', 
    'xp.compiler.types.TypeName'
  );

  /**
   * Abstract base class for all routines
   *
   * @see   xp://xp.compiler.ast.MethodNode
   * @see   xp://xp.compiler.ast.ConstructorNode
   * @see   xp://xp.compiler.ast.OperatorNode
   */
  abstract class RoutineNode extends TypeMemberNode {
    public $comment    = NULL;
    public $body       = NULL;
    public $parameters = array();
    public $throws     = array();
    
    /**
     * Adds a statement
     *
     * @param   xp.compiler.types.Node
     */
    public function addStatement(xp·compiler·ast·Node $statement) {
      $this->body[]= $statement;
    }

    /**
     * Returns this members's hashcode
     *
     * @return  string
     */
    public function hashCode() {
      return $this->getName().'()';
    }
  }
?>
