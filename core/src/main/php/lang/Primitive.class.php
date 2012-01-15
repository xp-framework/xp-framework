<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'lang.Type',
    'lang.types.String',
    'lang.types.Integer',
    'lang.types.Double',
    'lang.types.Boolean',
    'lang.types.ArrayList'
  );

  /**
   * Represents primitive types:
   * 
   * <ul>
   *   <li>string</li>
   *   <li>int</li>
   *   <li>double</li>
   *   <li>bool</li>
   * </ul>
   *
   * @test     xp://net.xp_framework.unittest.reflection.PrimitiveTest 
   * @see      xp://lang.Type
   * @purpose  Type implementation
   */
  class Primitive extends Type {
    public static
      $STRING  = NULL,
      $INT     = NULL,
      $DOUBLE  = NULL,
      $BOOL    = NULL,
      $BOOLEAN = NULL,    // deprecated
      $ARRAY   = NULL,    // deprecated
      $INTEGER = NULL;    // deprecated
    
    static function __static() {
      self::$STRING= new self('string', '');
      self::$INTEGER= self::$INT= new self('int', 0);
      self::$DOUBLE= new self('double', 0.0);
      self::$BOOLEAN= self::$BOOL= new self('bool', FALSE);
      self::$ARRAY= new self('array', array());
    }
    
    /**
     * Creates a new primitive instance
     *
     * @param   string name
     * @param   var default
     */
    public function __construct($name, $default) {
      parent::__construct($name);
      $this->default= $default;
    }
    
    /**
     * Returns the wrapper class for this primitive
     *
     * @see     http://en.wikipedia.org/wiki/Wrapper_class
     * @return  lang.XPClass
     */
    public function wrapperClass() {
      switch ($this) {
        case self::$STRING: return XPClass::forName('lang.types.String');
        case self::$INT: return XPClass::forName('lang.types.Integer');
        case self::$DOUBLE: return XPClass::forName('lang.types.Double');
        case self::$BOOL: return XPClass::forName('lang.types.Boolean');
        case self::$ARRAY: return XPClass::forName('lang.types.ArrayList'); // deprecated
      }
    }

    /**
     * Returns a new instance of this object
     *
     * @param   var value
     * @return  var
     */
    public function newInstance($value= NULL) {
      if (!$this->isInstance($value)) {
        raise('lang.IllegalArgumentException', 'Cannot create instances of the '.$this->name.' type from '.xp::typeOf($value));
      }
      return $value;
    }

    /**
     * Returns a new instance of this object
     *
     * @param   var value
     * @return  var
     */
    public function cast($value) {
      if (!$this->isInstance($value)) {
        raise('lang.ClassCastException', 'Cannot cast '.xp::typeOf($value).' to the '.$this->name.' type');
      }
      return $value;
    }
    
    /**
     * Boxes a type - that is, turns Generics into primitives
     *
     * @param   var in
     * @return  var the primitive if not already primitive
     * @throws  lang.IllegalArgumentException in case in cannot be unboxed.
     */
    public static function unboxed($in) {
      if ($in instanceof String) return $in->toString();
      if ($in instanceof Double) return $in->floatValue();
      if ($in instanceof Integer) return $in->intValue();
      if ($in instanceof Boolean) return $in->value;
      if ($in instanceof ArrayList) return $in->values;   // deprecated
      if ($in instanceof Generic) {
        throw new IllegalArgumentException('Cannot unbox '.xp::typeOf($in));
      }
      return $in; // Already primitive
    }
  
    /**
     * Boxes a type - that is, turns primitives into Generics
     *
     * @param   var in
     * @return  lang.Generic the Generic if not already generic
     * @throws  lang.IllegalArgumentException in case in cannot be boxed.
     */
    public static function boxed($in) {
      if (NULL === $in || $in instanceof Generic) return $in;
      $t= gettype($in);
      if ('string' === $t) return new String($in);
      if ('integer' === $t) return new Integer($in);
      if ('double' === $t) return new Double($in);
      if ('boolean' === $t) return new Boolean($in);
      if ('array' === $t) return ArrayList::newInstance($in);   // deprecated
      throw new IllegalArgumentException('Cannot box '.xp::typeOf($in));
    }
    
    /**
     * Get a type instance for a given name
     *
     * @param   string name
     * @return  lang.Type
     * @throws  lang.IllegalArgumentException if the given name does not correspond to a primitive
     */
    public static function forName($name) {
      switch ($name) {
        case 'string': return self::$STRING;
        case 'int': return self::$INT;
        case 'double': return self::$DOUBLE;
        case 'bool': return self::$BOOL;
        case 'array': return self::$ARRAY;    // deprecated
        case 'integer': return self::$INT;    // deprecated
        default: throw new IllegalArgumentException('Not a primitive: '.$name);
      }
    }

    /**
     * Returns type literal
     *
     * @return  string
     */
    public function literal() {
      return '�'.$this->name;
    }

    /**
     * Determines whether the specified object is an instance of this
     * type. 
     *
     * @param   var obj
     * @return  bool
     */
    public function isInstance($obj) {
      return $obj === NULL || $obj instanceof Generic 
        ? FALSE 
        : $this === Type::forName(gettype($obj))
      ;
    }
  }
?>
