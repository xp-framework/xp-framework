<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents a routine
   *
   * @model    abstract
   * @see      xp://lang.reflect.Method
   * @see      xp://lang.reflect.Constructor
   * @purpose  Reflection
   */
  class Routine extends Object {
    var
      $_ref = NULL,
      $name = '';

    /**
     * Constructor
     *
     * @access  private
     * @param   &mixed ref
     * @param   string name
     */    
    function __construct(&$ref, $name) {
      parent::__construct();
      $this->_ref= is_object($ref) ? get_class($ref) : $ref;
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @access  public
     * @return  string
     */
    function getName() {
      return $this->name;
    }
    
    /**
     * Returns the XPClass object representing the class or interface 
     * that declares the method represented by this Method object.
     *
     * Note that this method returns the first class in the inheritance
     * chain this method was declared in. This is due to inefficiency
     * in PHP4.
     *
     * @access  public
     * @return  &lang.XPClass
     */
    function &getDeclaringClass() {
      $c= $this->_ref;
      do {
        $p= get_parent_class($c);
        if (!$p || !is_callable(array($p, $this->name))) break;
      } while ($c= $p);

      return new XPClass($c);
    }
  }
?>
