<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Holds a reference to a class
   *
   * @see      xp://remote.Serializer
   * @purpose  Class reference
   */
  class ClassReference extends Object {
    var 
      $classname = '';

    /**
     * Constructor
     *
     * @access  public
     * @param   string classname
     */
    function __construct($classname) {
      $this->classname= $classname;
    }

    /**
     * Retrieved referenced class name
     *
     * @access  public
     * @return  string
     */
    function referencedName() {
      return $this->classname;
    }

    /**
     * Retrieved referenced class name
     *
     * @access  public
     * @param   lang.ClassLoader cl default NULL
     * @return  &lang.XPClass
     */
    function &referencedClass($cl= NULL) {
      return XPClass::forName($this->classname, $cl);
    }
  }
?>
