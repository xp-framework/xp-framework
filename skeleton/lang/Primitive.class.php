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
   *   <li>boolean</li>
   *   <li>array</li>
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
      $BOOLEAN = NULL,
      $ARRAY   = NULL,
      $INTEGER = NULL;    // deprecated
    
    static function __static() {
      self::$STRING= new self('string');
      self::$INTEGER= self::$INT= new self('int');
      self::$DOUBLE= new self('double');
      self::$BOOLEAN= new self('boolean');
      self::$ARRAY= new self('array');
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
        case self::$BOOLEAN: return XPClass::forName('lang.types.Boolean');
        case self::$ARRAY: return XPClass::forName('lang.types.ArrayList');
      }
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
      if ($in instanceof ArrayList) return $in->values;
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
      if ('array' === $t) return ArrayList::newInstance($in);
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
        case 'boolean': return self::$BOOLEAN;
        case 'array': return self::$ARRAY;
        case 'integer': return self::$INT;    // deprecated
        default: throw new IllegalArgumentException('Not a primitive: '.$name);
      }
    }
  }
?>
