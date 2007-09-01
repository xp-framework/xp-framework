<?php
/* This class is part of the XP framework
 *
 * $Id: Type.class.php 10162 2007-04-29 17:04:39Z friebe $ 
 */

  namespace lang;

  /**
   * Type is the base class for the XPClass and Primitive classes.
   *
   * @see      xp://lang.XPClass
   * @see      xp://lang.Primitive
   * @purpose  Abstract base class
   */
  abstract class Type extends Object {
    public
      $name= '';

    /**
     * Constructor
     *
     * @param   string name
     */
    public function __construct($name) {
      $this->name= $name;
    }

    /**
     * Retrieves the fully qualified class name for this class.
     * 
     * @return  string name - e.g. "io.File", "rdbms.mysql.MySQL"
     */
    public function getName() {
      return $this->name;
    }
    
    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'<'.$this->name.'>';
    }

    /**
     * Checks whether a given object is equal to this type
     *
     * @param   lang.Generic cmp
     * @return  bool
     */
    public function equals($cmp) {
      return $cmp instanceof self && $cmp->name === $this->name;
    }

    /**
     * Returns a hashcode for this object
     *
     * @return  string
     */
    public function hashCode() {
      return get_class($this).':'.$this->name;
    }
  }
?>
