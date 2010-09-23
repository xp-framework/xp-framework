<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.compiler.ast.TypeMemberNode', 'xp.compiler.types.TypeName');

  /**
   * Represents a class constant
   *
   * <code>
   *   class HttpMethods {
   *     const string GET  = 'GET';
   *     const string POST = 'POST';
   *     const string HEAD = 'HEAD';
   *   }
   * 
   *   $get= HttpMethods::GET;
   * </code>
   *
   * Class constants are limited to numbers, booleans and strings
   * but provide a cheap way of extracting magic constants from 
   * business logic. If you require more flexibility, use static 
   * fields.
   *
   * @see   xp://xp.compiler.ast.FieldNode
   */
  class ClassConstantNode extends TypeMemberNode {
    public $type= NULL;
    public $value= NULL;

    /**
     * Constructor
     *
     * @param   xp.compiler.ast.Node expression
     * @param   xp.compiler.ast.Node[] statements
     */
    public function __construct($name, TypeName $type, xp·compiler·ast·Node $value) {
      $this->name= $name;
      $this->type= $type;
      $this->value= $value;
    }

    /**
     * Returns this members's hashcode
     *
     * @return  string
     */
    public function hashCode() {
      return $this->getName();
    }
  }
?>
