<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents a class field
   *
   * @see      xp://lang.XPClass
   * @purpose  Reflection
   */
  class Field extends Object {
    protected
      $_reflection = NULL;

    /**
     * Constructor
     *
     * @access  public
     * @param   &mixed ref
     * @param   string name
     */    
    public function __construct($_reflection) {
      $this->_reflection= $_reflection;
    }

    /**
     * Get Name
     *
     * @access  public
     * @return  string
     */
    public function getName() {
      return $this->_reflection->getName();
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
    public function getDeclaringClass() {
      return new XPClass($this->_reflection->getDeclaringClass());
    }
  }
?>
