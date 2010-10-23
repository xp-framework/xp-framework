<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.Primitive', 'lang.ArrayType', 'lang.MapType');

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
     * Creates a type list from a given string
     *
     * @param   string names
     * @return  lang.Type[] list
     */
    public static function forNames($names) {
      $types= array();
      for ($args= $names.',', $o= 0, $brackets= 0, $i= 0, $s= strlen($args); $i < $s; $i++) {
        if (',' === $args{$i} && 0 === $brackets) {
          $types[]= self::forName(ltrim(substr($args, $o, $i- $o)));
          $o= $i+ 1;
        } else if ('<' === $args{$i}) {
          $brackets++;
        } else if ('>' === $args{$i}) {
          $brackets--;
        }
      }
      return $types;
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
      static $deprecated= array(
        'char'      => 'string',
        'integer'   => 'int',
        'boolean'   => 'bool',
        'float'     => 'double',
        'mixed'     => 'var',
        '*'         => 'var',
        'array'     => 'var[]',
        'resource'  => 'var'
      );
      
      // Map deprecated type names
      $type= isset($deprecated[$name]) ? $deprecated[$name] : $name;
      
      // Map well-known primitives, var and void, handle rest syntactically:
      // * T[] is an array
      // * [:T] is a map 
      // * T* is a vararg
      // * T<K, V> is a generic
      // * Anything else is a qualified or unqualified class name
      if ('string' === $type || 'int' === $type || 'double' === $type || 'bool' == $type) {
        return Primitive::forName($type);
      } else if ('var' === $type) {
        return self::$VAR;
      } else if ('void' === $type) {
        return self::$VOID;
        return $type;
      } else if ('[]' === substr($type, -2)) {
        return ArrayType::forName($type);
      } else if ('[:' === substr($type, 0, 2)) {
        return MapType::forName($type);
      } else if ('*' === substr($type, -1)) {
        return ArrayType::forName(substr($type, 0, -1).'[]');
      } else if (FALSE === ($p= strpos($type, '<'))) {
        return strstr($type, '.') ? XPClass::forName($type) : new XPClass($type);
      }
      
      // Generics
      // * D<K, V> is a generic type definition D with K and V componenty
      // * Deprecated: array<T> is T[], array<K, V> is [:T]
      $base= substr($type, 0, $p);
      $components= self::forNames(substr($type, $p+ 1, -1));
      if ('array' !== $base) {
        return cast(self::forName($base), 'lang.XPClass')->newGenericType($components);
      }
      
      $s= sizeof($components);
      if (2 === $s) {
        return MapType::forName('[:'.$components[1]->name.']');
      } else if (1 === $s) {
        return ArrayType::forName($components[0]->name.'[]');
      }

      throw new IllegalArgumentException('Unparseable name '.$name);
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
