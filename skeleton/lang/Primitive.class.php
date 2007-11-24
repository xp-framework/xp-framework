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
   *   <li>integer</li>
   *   <li>double</li>
   *   <li>boolean</li>
   *   <li>array</li>
   * </ul>
   *
   * @see      xp://lang.Type
   * @purpose  Type implementation
   */
  class Primitive extends Type {
    public static
      $STRING  = NULL,
      $INTEGER = NULL,
      $DOUBLE  = NULL,
      $BOOLEAN = NULL,
      $ARRAY   = NULL;
    
    static function __static() {
      self::$STRING= new self('string');
      self::$INTEGER= new self('integer');
      self::$DOUBLE= new self('double');
      self::$BOOLEAN= new self('boolean');
      self::$ARRAY= new self('array');
    }
    
    /**
     * Boxes a type - that is, turns Generics into primitives
     *
     * @param   mixed in
     * @return  mixed the primitive if not already primitive
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
     * @param   mixed in
     * @return  lang.Generic the Generic if not already generic
     * @throws  lang.IllegalArgumentException in case in cannot be boxed.
     */
    public static function boxed($in) {
      if ($in instanceof Generic) return $in;
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
        case 'integer': return self::$INTEGER;
        case 'double': return self::$DOUBLE;
        case 'boolean': return self::$BOOLEAN;
        case 'array': return self::$ARRAY;
        default: throw new IllegalArgumentException('Not a primitive: '.$name);
      }
    }
  }
?>
