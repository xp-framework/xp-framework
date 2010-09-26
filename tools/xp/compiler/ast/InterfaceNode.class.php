<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.compiler.ast.TypeDeclarationNode');

  /**
   * Represents an interface declaration
   *
   */
  class InterfaceNode extends TypeDeclarationNode {
    public $parents= NULL;

    /**
     * Constructor
     *
     * @param   int modifiers
     * @param   xp.compiler.ast.AnnotationNode[] annotations
     * @param   xp.compiler.types.TypeName name
     * @param   xp.compiler.types.TypeName[] parents
     * @param   xp.compiler.ast.Node[] body
     */
    public function __construct($modifiers= 0, array $annotations= NULL, TypeName $name= NULL, array $parents= NULL, array $body= NULL) {
      $this->modifiers= $modifiers;
      $this->annotations= $annotations;
      $this->name= $name;
      $this->parents= $parents;
      $this->setBody($body);
    }
  }
?>
