<?php
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
     * @param   string class fully qualified class name
     * @param   string name enumeration member
     * @return  lang.Enum
     */
    public static function valueOf($class, $name) {
      try {
        $c= new ReflectionClass($class);
        return $c->getStaticPropertyValue($name);
      } catch (ReflectionException $e) {
        throw new IllegalArgumentException($e->getMessage());
      }
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
      $c= new ReflectionClass($class);
      return array_values($c->getStaticProperties());
    }
  }
?>
