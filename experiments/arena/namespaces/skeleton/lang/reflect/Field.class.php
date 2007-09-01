<?php
/* This class is part of the XP framework
 *
 * $Id: Field.class.php 9090 2007-01-03 13:57:55Z friebe $ 
 */

  namespace lang::reflect;

  /**
   * Represents a class field
   *
   * @test     xp://net.xp_framework.unittest.reflection.ReflectionTest
   * @see      xp://lang.XPClass
   * @purpose  Reflection
   */
  class Field extends lang::Object {
    public
      $_ref   = NULL,
      $name   = '',
      $type   = NULL;

    protected
      $_reflect = NULL;

    /**
     * Constructor
     *
     * @param   mixed ref
     * @param   string name
     * @param   string type default NULL
     */    
    public function __construct($ref, $name, $type= NULL) {
      $this->_ref= is_object($ref) ? get_class($ref) : $ref;
      $this->name= $name;
      $this->type= $type;
      $this->_reflect= new ::ReflectionProperty($this->_ref, $this->name);
    }

    /**
     * Get field's name.
     *
     * @return  string
     */
    public function getName() {
      return $this->name;
    }
    
    /**
     * Gets field type
     *
     * @return  string
     */
    public function getType() {
      if (isset($this->type)) return $this->type;
      if ($details= lang::XPClass::detailsForField($this->_ref, $this->name)) {
        if (isset($details[DETAIL_ANNOTATIONS]['type'])) return $details[DETAIL_ANNOTATIONS]['type'];
      }
      return NULL;
    }
    
    /**
     * Returns the XPClass object representing the class or interface 
     * that declares the field represented by this Field object.
     *
     * @return  lang.XPClass
     */
    public function getDeclaringClass() {
      return new lang::XPClass($this->_reflect->getDeclaringClass()->getName());
    }
    
    /**
     * Returns the value of the field represented by this Field, on the 
     * specified object.
     *
     * @param   lang.Object instance
     * @return  mixed  
     * @throws  lang.IllegalArgumentException in case the passed object is not an instance of the declaring class
     */
    public function get($instance) {
      if (!::is(::xp::nameOf($this->_ref), $instance)) {
        throw(new lang::IllegalArgumentException(sprintf(
          'Passed argument is not a %s class (%s)',
          ::xp::nameOf($this->_ref),
          ::xp::nameOf($instance)
        )));
      }

      return $instance->{$this->name};
    }

    /**
     * Retrieve this field's modifiers
     *
     * @see     xp://lang.reflect.Modifiers
     * @return  int
     */    
    public function getModifiers() {
      return $this->_reflect->getModifiers();
    }
    
    /**
     * Creates a string representation of this field
     *
     * @return  string
     */
    public function toString() {
      return Modifiers::stringOf($this->getModifiers()).' '.$this->getType().' $'.$this->name;
    }
  }
?>
