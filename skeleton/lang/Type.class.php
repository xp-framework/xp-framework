<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.Primitive', 'lang.ArrayType');

  /**
   * Type is the base class for the XPClass and Primitive classes.
   *
   * @see      xp://lang.XPClass
   * @see      xp://lang.Primitive
   * @test     xp://net.xp_framework.unittest.reflection.TypeTest 
   * @purpose  Base class
   */
  class Type extends Object {
    public static
      $ANY,           // deprecated
      $VAR,
      $VOID;

    public
      $name= '';

    static function __static() {
      self::$ANY= self::$VAR= new self('var');
      self::$VOID= new self('void');
    }

    /**
     * Constructor
     *
     * @param   string name
     */
    public function __construct($name) {
      $this->name= $name;
    }

    /**
     * Retrieves the fully qualified class name for this class.
     * 
     * @return  string name - e.g. "io.File", "rdbms.mysql.MySQL"
     */
    public function getName() {
      return $this->name;
    }
    
    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'<'.$this->name.'>';
    }

    /**
     * Checks whether a given object is equal to this type
     *
     * @param   lang.Generic cmp
     * @return  bool
     */
    public function equals($cmp) {
      return $cmp instanceof self && $cmp->name === $this->name;
    }

    /**
     * Returns a hashcode for this object
     *
     * @return  string
     */
    public function hashCode() {
      return get_class($this).':'.$this->name;
    }
    
    /**
     * Gets a type for a given name
     *
     * Checks for:
     * <ul>
     *   <li>Primitive types (string, integer, double, boolean, array)</li>
     *   <li>Array notations (string[] or string*)</li>
     *   <li>Resources</li>
     *   <li>Any type (var or *)</li>
     *   <li>Generic notations (util.collections.HashTable<lang.types.String, lang.Generic>)</li>
     *   <li>Anything else will be passed to XPClass::forName()</li>
     * </ul>
     *
     * @param   string name
     * @return  lang.Type
     */
    public static function forName($name) {
      switch ($name) {
        case 'string': 
        case 'char': 
          return Primitive::$STRING;

        case 'integer': 
        case 'int': 
          return Primitive::$INT;

        case 'double': 
        case 'float': 
          return Primitive::$DOUBLE;

        case 'boolean': 
        case 'bool': 
          return Primitive::$BOOLEAN;

        case 'var': 
        case '*': 
        case 'mixed': 
          return self::$VAR;

        case 'array': 
          return ArrayType::forName('var[]');
          
        case '*' == substr($name, -1): 
          return ArrayType::forName(substr($name, 0, -1).'[]');

        case '[]' === substr($name, -2): 
          return ArrayType::forName($name);

        case 'resource':    // XXX FIXME
          return Primitive::$INT;
        
        case 'void':
          return self::$VOID;
        
        case FALSE !== ($p= strpos($name, '<')):
          if ('array' === ($base= substr($name, 0, $p))) return Primitive::$ARRAY;
          foreach (explode(',', substr($name, $p+ 1, -1)) as $type) {
            $typeargs[]= self::forName(ltrim($type));
          }
          return XPClass::forName(strstr($base, '.') ? $base : xp::nameOf($base))->newGenericType($typeargs);

        case FALSE === strpos($name, '.'): 
          return new XPClass(new ReflectionClass($name));
        
        default:
          return XPClass::forName($name);
      }
    }
    
    /**
     * Returns type literal
     *
     * @return  string
     */
    public function literal() {
      return $this->name;
    }

    /**
     * Determines whether the specified object is an instance of this
     * type. 
     *
     * @param   var obj
     * @return  bool
     */
    public function isInstance($obj) {
      return self::$VAR === $this;      // VAR is always true, VOID never
    }
  }
?>
