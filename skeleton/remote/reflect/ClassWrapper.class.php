<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Describes a class 
   *
   * @see      xp://remote.Remote
   * @purpose  Reflection
   */
  class ClassWrapper extends Object {
    var 
      $className   = '',
      $fields      = array(); 

    /**
     * Creates a string representation of this object
     *
     * @access  public
     * @return  string
     */
    function toString() {
      $fields= '';
      foreach (array_keys($this->fields) as $name) {
        $fields.= sprintf("  [%-20s] %s\n", $name, xp::stringOf($this->fields[$name]));
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
