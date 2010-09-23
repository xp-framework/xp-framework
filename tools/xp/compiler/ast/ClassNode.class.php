<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.compiler.ast.TypeDeclarationNode');

  /**
   * Represents a class declaration
   *
   */
  class ClassNode extends TypeDeclarationNode {
    public $parent= NULL;
    public $implements= NULL;
    public $literal= NULL;
    
    /**
     * Constructor
     *
     * @param   int modifiers
     * @param   xp.compiler.ast.AnnotationNode[] annotations
     * @param   xp.compiler.types.TypeName name
     * @param   xp.compiler.types.TypeName parent
     * @param   xp.compiler.types.TypeName[] implements
     * @param   xp.compiler.ast.Node[] body
     */
    public function __construct($modifiers= 0, array $annotations= NULL, TypeName $name= NULL, TypeName $parent= NULL, array $implements= NULL, array $body= NULL) {
      $this->modifiers= $modifiers;
      $this->annotations= $annotations;
      $this->name= $name;
      $this->parent= $parent;
      $this->implements= $implements;
      $this->setBody($body);
    }
  }
?>
