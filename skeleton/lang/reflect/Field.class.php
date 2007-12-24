<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents a class field
   *
   * @test     xp://net.xp_framework.unittest.reflection.FieldsTest
   * @see      xp://lang.XPClass
   * @purpose  Reflection
   */
  class Field extends Object {
    protected
      $_class   = NULL;

    public
      $_reflect = NULL;

    /**
     * Constructor
     *
     * @param   string class
     * @param   php.ReflectionProperty reflect
     */    
    public function __construct($class, $reflect) {
      $this->_class= $class;
      $this->_reflect= $reflect;
    }

    /**
     * Get field's name.
     *
     * @return  string
     */
    public function getName() {
      return $this->_reflect->getName();
    }
    
    /**
     * Gets field type
     *
     * @return  string
     */
    public function getType() {
      if ($details= XPClass::detailsForField($this->_class, $this->_reflect->getName())) {
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
      return new XPClass($this->_reflect->getDeclaringClass()->getName());
    }
    
    /**
     * Returns the value of the field represented by this Field, on the 
     * specified object.
     *
     * @param   lang.Object instance
     * @return  mixed  
     * @throws  lang.IllegalArgumentException in case the passed object is not an instance of the declaring class
     * @throws  lang.IllegalAccessException in case this field is not public
     */
    public function get($instance) {
    
      // Verify the field is public
      if (!($this->_reflect->getModifiers() & MODIFIER_PUBLIC)) {
        throw new IllegalAccessException('Cannot read '.$this->toString());
      }

      // Short-circuit further checks for static members
      if ($this->_reflect->isStatic()) {
        return $this->_reflect->getValue(NULL);
      }

      // Verify given instance is instance of the class declaring this 
      // property
      if (!($instance instanceof $this->_class)) {
        throw new IllegalArgumentException(sprintf(
          'Passed argument is not a %s class (%s)',
          xp::nameOf($this->_class),
          xp::typeOf($instance)
        ));
      }


      return $this->_reflect->getValue($instance);
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
      $t= $this->getType();
      return sprintf(
        '%s%s %s::$%s',
        Modifiers::stringOf($this->getModifiers()),
        $t ? ' '.$t : '',
        $this->getDeclaringClass()->getName(),
        $this->getName()
      );
    }
  }
?>
