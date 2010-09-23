<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'xp.compiler.ast.Node', 
    'xp.compiler.ast.MethodNode',
    'xp.compiler.ast.FieldNode',
    'xp.compiler.ast.AnnotationNode',
    'xp.compiler.types.TypeName'
  );
  
  /**
   * Represents a type declaration
   *
   * @see      xp://xp.compiler.ast.ClassNode
   * @see      xp://xp.compiler.ast.InterfaceNode
   * @see      xp://xp.compiler.ast.EnumNode
   */
  abstract class TypeDeclarationNode extends xp·compiler·ast·Node {
    public $modifiers= 0;
    public $annotations= NULL;
    public $name= NULL;
    public $body= NULL;
    public $comment= NULL;
    public $synthetic= FALSE;
    
    /**
     * Sets this type's body
     *
     * @param   xp.compiler.ast.Node[] body
     */
    public function setBody(array $body= NULL) {
      $this->body= $body;
    }
  }
?>
