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
    protected static
      $STRING     = NULL,
      $INTEGER    = NULL,
      $DOUBLE     = NULL,
      $BOOLEAN    = NULL,
      $ARRAYLIST  = NULL;
    
    static function __static() {
      self::$STRING= new self('string');
      self::$INTEGER= new self('integer');
      self::$DOUBLE= new self('double');
      self::$BOOLEAN= new self('boolean');
      self::$ARRAYLIST= new self('array');
    }
    
    /**
     * Boxes a type - that is, turns primitives into Generics
     *
     * @param   lang.Generic
     * @return  mixed 
     */
    public static function unboxed($in) {
      switch (1) {
        case $in instanceof String: 
          return $in->getBuffer();

        case $in instanceof Double: 
          return $in->floatValue();
        
        case $in instanceof Integer:
          return $in->intValue();

        case $in instanceof Boolean:
          return $in->value;
        
        case $in instanceof ArrayList:
          return $in->values;
          
        case $in instanceof Generic:
          throw new IllegalArgumentException('Cannot box '.xp::typeOf($in));
        
        default:
          return $in;
      }
    }
  
    /**
     * Boxes a type - that is, turns primitives into Generics
     *
     * @param   mixed in
     * @return  lang.Generic
     */
    public static function boxed($in) {
      if ($in instanceof Generic) return $in; else switch (gettype($in)) {
        case 'string': return new String($in);
        case 'integer': return new Integer($in);
        case 'double': return new Double($in);
        case 'boolean': return new Boolean($in);
        case 'array': return ArrayList::newInstance($in);
        default: throw new IllegalArgumentException('Cannot box '.xp::typeOf($in));
      }
    }
    
    /**
     * Get a type instance for a given name
     *
     * @param   string name
     * @return  lang.Type
     */
    public static function forName($name) {
      switch ($name) {
        case 'string': return self::$STRING;
        case 'integer': return self::$INTEGER;
        case 'double': return self::$DOUBLE;
        case 'boolean': return self::$BOOLEAN;
        case 'array': return self::$ARRAYLIST;
        default: return new self($name, FALSE);
      }
    }
  }
?>
