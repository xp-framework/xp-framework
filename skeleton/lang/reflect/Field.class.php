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
      $name   = '',
      $value  = NULL;

    /**
     * Constructor
     *
     * @access  private
     * @param   &mixed ref
     * @param   string name
     */    
    function __construct(&$ref, $name, &$value) {
      $this->_ref= is_object($ref) ? get_class($ref) : $ref;
      $this->name= $name;
      $this->value= $value;
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
    
  }
?>
