<?php
/* This class is part of the XP framework
 *
 * $Id: ClassWrapper.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace remote::reflect;

  uses('util.collections.HashSet');

  /**
   * Describes a class 
   *
   * @see      xp://remote.Remote
   * @purpose  Reflection
   */
  class ClassWrapper extends lang::Object {
    public 
      $className   = '',
      $fields      = array(); 

    /**
     * Retrieve a set of classes used in this interface
     *
     * @return  remote.ClassReference[]
     */
    public function classSet() {
      $set= new util::collections::HashSet();
      foreach (array_keys($this->fields) as $name) {
        if (!is('ClassReference', $this->fields[$name])) continue;
        
        $set->add($this->fields[$name]);
      }
      return $set->toArray();
    }

    /**
     * Get ClassName
     *
     * @return  string
     */
    public function getName() {
      return $this->className;
    }

    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      $fields= '';
      foreach (array_keys($this->fields) as $name) {
        $fields.= sprintf("  [%-20s] %s\n", $name, ::xp::stringOf($this->fields[$name]));
      }
      return sprintf(
        "%s@(name= %s) {\n%s}",
        $this->getClassName(),
        $this->className,
        $fields
      );
    }
  }
?>
