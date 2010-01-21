<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * COM object
   * 
   * <quote>
   * COM is a technology which allows the reuse of code written in any language 
   * (by any language) using a standard calling convention and hiding behind 
   * APIs the implementation details such as what machine the Component is 
   * stored on and the executable which houses it. It can be thought of as a 
   * super Remote Procedure Call (RPC) mechanism with some basic object roots. 
   * It separates implementation from interface.
   * 
   * COM encourages versioning, separation of implementation from interface and 
   * hiding the implementation details such as executable location and the 
   * language it was written in.
   * </quote>
   *
   * @see      http://www.microsoft.com/Com/resources/comdocs.asp COM specification
   * @see      http://www.developmentor.com/dbox/yacl.htm Yet Another COM Library (YACL) 
   * @ext      com
   * @purpose  Base class
   * @platform Windows
   */
  class COMObject extends Object {
    protected
      $h   = NULL;
  
    /**
     * Constructor
     *
     * @param   string identifier
     * @param   string server default NULL
     */    
    public function __construct($identifier, $server= NULL) {
      if ($identifier instanceof COM || $identifier instanceof Variant) {
        $this->h= $identifier;
      } else {
        try {
          $this->h= new COM($identifier, $server);
        } catch (com_exception $e) {
          throw new IllegalArgumentException($e->getCode().': '.$e->getMessage());
        }
      }
    }
    
    /**
     * Magic interceptor for member read access
     *
     * @param   string name
     * @return  var value
     */
    public function __get($name) {
      try {
        $v= $this->h->{$name};
        if ($v instanceof COM || $v instanceof Variant) {
          return new self($v);
        } else {
          return $v;
        }
      } catch (com_exception $e) {
        throw new IllegalArgumentException($e->getCode().': '.$e->getMessage());
      }
    }
    
    /**
     * Magic interceptor for member write access
     *
     * @param   string name
     * @param   var value
     */
    public function __set($name, $value) {
      try {
        if ($value instanceof self) {
          $this->h->{$name}= $value->h;
        } else {
          $this->h->{$name}= $value;
        }
      } catch (com_exception $e) {
        throw new IllegalArgumentException($e->getCode().': '.$e->getMessage());
      }
    }
    
    /**
     * Magic interceptor for member method access
     *
     * @param   string name
     * @param   var[] args
     * @return  var return
     */
    public function __call($name, $args) {
      $s= '';
      foreach ($args as $i => $value) {
        if ($value instanceof self) {
          $s.= ', $args['.$i.']->h';
        } else {
          $s.= ', $args['.$i.']';
        }
      }
      try {
        $v= eval('return $this->h->'.$name.'('.substr($s, 2).');');
        if ($v instanceof COM || $v instanceof Variant) {
          return new self($v);
        } else {
          return $v;
        }
      } catch (com_exception $e) {
        throw new IllegalArgumentException($e->getCode().': '.$e->getMessage());
      }
    }
    
    /**
     * Destructor
     *
     */
    public function __destruct() {
      $this->h= NULL;
    }
    
    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'(->'.xp::stringOf($this->h).')';
    }
  }
?>
