<?php
/* This class is part of the XP framework
 *
 * $Id: ArrayType.class.php 14822 2010-09-14 07:51:30Z friebe $
 */

  uses('lang.Type');

  /**
   * Represents map types
   *
   * @see      xp://lang.Type
   * @test     xp://net.xp_framework.unittest.core.MapTypeTest
   * @purpose  Type implementation
   */
  class MapType extends Type {
  
    /**
     * Gets this array's component type
     *
     * @return  lang.Type
     */
    public function componentType() {
      return Type::forName(substr($this->name, 2, -1));
    }

    /**
     * Get a type instance for a given name
     *
     * @param   string name
     * @return  lang.ArrayType
     * @throws  lang.IllegalArgumentException if the given name does not correspond to a primitive
     */
    public static function forName($name) {
      if ('[:' !== substr($name, 0, 2)) throw new IllegalArgumentException('Not a map: '.$name);
      
      return new self($name);
    }

    /**
     * Returns type literal
     *
     * @return  string
     */
    public function literal() {
      return '»'.$this->componentType()->literal();
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
  }
?>
