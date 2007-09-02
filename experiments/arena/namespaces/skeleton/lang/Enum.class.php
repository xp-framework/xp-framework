<?php
/* This class is part of the XP framework
 *
 * $Id: Enum.class.php 10925 2007-08-21 18:58:00Z friebe $ 
 */

  namespace lang;

  /**
   * Enumeration
   *
   * @purpose  Abstract base class   
   */
  abstract class Enum extends Object {
    public
      $name     = '';
    
    protected
      $ordinal  = 0;
  
    /**
     * Constructor
     *
     * @param   int ordinal default 0
     * @param   string name default ''
     */
    public function __construct($ordinal= 0, $name= '') {
      $this->ordinal= $ordinal;
      $this->name= $name;
    }
  
    /**
     * Returns the enumeration member uniquely identified by 
     *
     * @param   lang.XPClass class class object
     * @param   string name enumeration member
     * @return  lang.Enum
     * @throws  lang.IllegalArgumentException in case the enum member does not exist or when the given class is not an enum
     */
    public static function valueOf( $class, $name) {
      if (!$class->isEnum()) {
        throw new IllegalArgumentException('Argument class must be lang.XPClass<? extends lang.Enum>');
      }
      try {
        return $class->_reflect->getStaticPropertyValue($name);
      } catch (::ReflectionException $e) {
        throw new IllegalArgumentException($e->getMessage());
      }
    }

    /**
     * Returns the enumeration member uniquely identified by 
     *
     * @param   lang.XPClass class class object
     * @return  lang.Enum[]
     * @throws  lang.IllegalArgumentException in case the given class is not an enum
     */
    public static function valuesOf( $class) {
      if (!$class->isEnum()) {
        throw new IllegalArgumentException('Argument class must be lang.XPClass<? extends lang.Enum>');
      }
      try {
        return array_values($class->_reflect->getStaticProperties());
      } catch (::ReflectionException $e) {
        throw new IllegalArgumentException($e->getMessage());
      }
    }
    
    /**
     * Clone interceptor - ensures enums cannot be cloned
     *
     * @throws  lang.CloneNotSupportedException'  
     */
    public final function __clone() {
      ::raise('lang.CloneNotSupportedException', 'Enums cannot be cloned');
    }

    /**
     * Returns the name of this enum constant, exactly as declared in its 
     * enum declaration.
     *
     * @return  string
     */
    public function name() {
      return $this->name;
    }
    
    /**
     * Returns the ordinal of this enumeration constant (its position in 
     * its enum declaration, where the initial constant is assigned an 
     * ordinal of zero).
     *
     * @return  int
     */
    public function ordinal() {
      return $this->ordinal;
    }

    /**
     * Create a string representation of this enum
     *
     * @return  string
     */
    public function toString() {
      return $this->name;
    }
    
    /**
     * Returns all members for a given enum.
     *
     * @param   string class
     * @return  lang.Enum[]
     */
    protected static function membersOf($class) {
      $c= new ::ReflectionClass($class);
      return array_values($c->getStaticProperties());
    }
  }
?>
