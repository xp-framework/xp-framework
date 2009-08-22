<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Enumeration
   *
   * @see      http://news.xp-framework.net/article/222/2007/11/12/
   * @see      http://news.xp-framework.net/article/207/2007/07/29/
   * @test     xp://net.xp_framework.unittest.core.EnumTest
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
    public static function valueOf(XPClass $class, $name) {
      if (!$class->isEnum()) {
        throw new IllegalArgumentException('Argument class must be lang.XPClass<? extends lang.Enum>');
      }
      try {
        $prop= $class->_reflect->getStaticPropertyValue($name);
        if ($prop instanceof self && $class->_reflect->isInstance($prop)) return $prop;
      } catch (ReflectionException $e) {
        throw new IllegalArgumentException($e->getMessage());
      }
      throw new IllegalArgumentException('No such member "'.$member.'" in '.$class->getName());
    }

    /**
     * Returns the enumeration member uniquely identified by 
     *
     * @param   lang.XPClass class class object
     * @return  lang.Enum[]
     * @throws  lang.IllegalArgumentException in case the given class is not an enum
     */
    public static function valuesOf(XPClass $class) {
      if (!$class->isEnum()) {
        throw new IllegalArgumentException('Argument class must be lang.XPClass<? extends lang.Enum>');
      }
      $r= array();
      foreach ($class->_reflect->getStaticProperties() as $prop) {
        $prop instanceof self && $class->_reflect->isInstance($prop) && $r[]= $prop;
      }
      return $r;
    }
    
    /**
     * Clone interceptor - ensures enums cannot be cloned
     *
     * @throws  lang.CloneNotSupportedException
     */
    public final function __clone() {
      raise('lang.CloneNotSupportedException', 'Enums cannot be cloned');
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
      $r= array();
      $c= new ReflectionClass($class);
      foreach ($c->getStaticProperties() as $prop) {
        $prop instanceof self && $c->isInstance($prop) && $r[]= $prop;
      }
      return $r;
    }
  }
?>
