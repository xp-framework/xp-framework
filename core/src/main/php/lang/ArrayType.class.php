<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('lang.Type');

  /**
   * Represents array types
   *
   * @see   xp://lang.Type
   * @test  xp://net.xp_framework.unittest.core.ArrayTypeTest
   */
  class ArrayType extends Type {

    /**
     * Creates a new array type instance
     *
     * @param  var component
     */
    public function __construct($component) {
      if ($component instanceof Type) {
        parent::__construct($component->getName().'[]');
      } else {
        parent::__construct($component.'[]');
      }
    }
  
    /**
     * Gets this array's component type
     *
     * @return  lang.Type
     */
    public function componentType() {
      return Type::forName(substr($this->name, 0, strpos($this->name, '[')));
    }

    /**
     * Get a type instance for a given name
     *
     * @param   string name
     * @return  lang.ArrayType
     * @throws  lang.IllegalArgumentException if the given name does not correspond to a primitive
     */
    public static function forName($name) {
      if ('[]' !== substr($name, -2)) {
        throw new IllegalArgumentException('Not an array: '.$name);
      }
      
      return new self(substr($name, 0, -2));
    }

    /**
     * Returns type literal
     *
     * @return  string
     */
    public function literal() {
      return '¦'.$this->componentType()->literal();
    }

    /**
     * Determines whether the specified object is an instance of this
     * type. 
     *
     * @param   var obj
     * @return  bool
     */
    public function isInstance($obj) {
      if (!is_array($obj)) return FALSE;

      $c= $this->componentType();
      foreach ($obj as $element) {
        if (!$c->isInstance($element)) return FALSE;
      }
      return TRUE;
    }

    /**
     * Tests whether this type is assignable from another type
     *
     * @param   var type
     * @return  bool
     */
    public function isAssignableFrom($type) {
      $t= $type instanceof Type ? $type : Type::forName($type);
      return $t instanceof self 
        ? $t->componentType()->isAssignableFrom($this->componentType())
        : FALSE
      ;
    }
  }
?>
