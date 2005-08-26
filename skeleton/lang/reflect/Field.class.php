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
    var
      $_ref   = NULL,
      $name   = '';

    /**
     * Constructor
     *
     * @access  private
     * @param   &mixed ref
     * @param   string name
     */    
    function __construct(&$ref, $name) {
      $this->_ref= is_object($ref) ? get_class($ref) : $ref;
      $this->name= $name;
    }

    /**
     * Get field's name.
     *
     * @access  public
     * @return  string
     */
    function getName() {
      return $this->name;
    }
    
    /**
     * Gets field type
     *
     * @access  public
     * @return  string
     */
    function getType() {
      if ($details= XPClass::detailsForField($this->_ref, $this->name)) {
        if (isset($details[DETAIL_ANNOTATIONS]['type'])) return $details[DETAIL_ANNOTATIONS]['type'];
      }
      return gettype($this->value);
    }
    
    /**
     * Returns the XPClass object representing the class or interface 
     * that declares the field represented by this Field object.
     *
     * @access  public
     * @return  &lang.XPClass
     */
    function &getDeclaringClass() {
      $class= $this->_ref;
      while ($details= XPClass::detailsForClass(xp::nameOf($class))) {
        if (isset($details[0][$this->name])) return new XPClass($class);
        $class= get_parent_class($class);
      }
      return xp::null();
    }
    
    /**
     * Returns the value of the field represented by this Field, on the 
     * specified object.
     *
     * @access  public
     * @param   &lang.Object instance
     * @return  &mixed  
     * @throws  lang.IllegalArgumentException in case the passed object is not an instance of the declaring class
     */
    function &get(&$instance) {
      if (!is(xp::nameOf($this->_ref), $instance)) {
        return throw(new IllegalArgumentException(sprintf(
          'Passed argument is not a %s class (%s)',
          xp::nameOf($this->_ref),
          xp::nameOf($instance)
        )));
      }

      return $instance->{$this->name};
    }
  }
?>
