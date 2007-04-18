<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'lang.XPClass',
    'lang.types.String',
    'lang.types.Integer',
    'lang.types.Double',
    'lang.types.Boolean',
    'lang.types.ArrayList'
  );

  /**
   * (Insert class' description here)
   *
   * @see      reference
   * @purpose  Base class for all types
   */
  class Type extends Object {
    protected
      $name= '';
    
    protected static
      $STRING,
      $INTEGER,
      $DOUBLE,
      $BOOLEAN,
      $ARRAYLIST;
    
    static function __static() {
      self::$STRING= new self('string');
      self::$INTEGER= new self('integer');
      self::$DOUBLE= new self('double');
      self::$BOOLEAN= new self('boolean');
      self::$ARRAYLIST= new self('array');
    }
    
    /**
     * Constructor
     *
     * @param   string name
     */
    public function __construct($name, $primitive= TRUE) {
      $this->name= $name;
      $this->primitive= $primitive;
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
     * Casts a type
     *
     * @param   mixed in
     * @return  lang.Generic
     */
    public function cast($in) {
      if ($this->primitive) {
        $v= self::unboxed($in);
        return eval('return ('.$this->name.')$v;');
      } else {
        $v= self::boxed($in);
        if (!($v instanceof $this->name)) throw new IllegalArgumentException(
          'Cannot cast '.xp::typeOf($in).' to '.$this->name
        );
        return $v;
      }
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
