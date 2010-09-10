<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('com.microsoft.com.COMObjectIterator');

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
   * @test     xp://com.microsoft.unittest.COMObjectTest
   * @platform Windows
   */
  class COMObject extends Object implements IteratorAggregate, ArrayAccess {
    protected $h= NULL;
  
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
      $pass= array();
      foreach ($args as $i => $value) {
        if ($value instanceof self) {
          $pass[]= $value->h;
        } else {
          $pass[]= $value;
        }
      }
      
      // call_user_func_array() will raise an error here if:
      // a) The method doesn't exists
      // b) The argument doesn't match the signature
      try {
        $l= __LINE__; $v= call_user_func_array(array($this->h, $name), $pass);
        if (isset(xp::$registry['errors'][__FILE__][$l])) {
          $error= key(xp::$registry['errors'][__FILE__][$l]);
          xp::gc(__FILE__);
          throw new IllegalArgumentException($error);
        }
      } catch (com_exception $e) {
        throw new IllegalArgumentException($e->getCode().': '.$e->getMessage());
      }
      
      if ($v instanceof COM || $v instanceof Variant) {
        return new self($v);
      } else {
        return $v;
      }
    }

    /**
     * Returns an iterator for use in foreach()
     *
     * @return  var
     */
    public function getIterator() {
      $iteration= array();
      foreach ($this->h as $i => $value) {
        $iteration[$i]= $value;
      }
      return new COMObjectIterator($iteration);
    }

    /**
     * = list[] overloading
     *
     * @param   var offset
     * @return  var
     * @throws  lang.IndexOutOfBoundsException if key does not exist
     */
    public function offsetGet($offset) {
      try {
        return $this->h[$offset];
      } catch (com_exception $e) {
        throw new IllegalArgumentException($e->getCode().': '.$e->getMessage());
      }
    }

    /**
     * list[]= overloading
     *
     * @param   var offset
     * @param   var value
     * @throws  lang.IllegalArgumentException if key is neither numeric (set) nor NULL (add)
     */
    public function offsetSet($offset, $value) {
      $this->h[$offset]= $value;
    }

    /**
     * isset() overloading
     *
     * @param   var offset
     * @return  bool
     */
    public function offsetExists($offset) {
      return isset($this->h[$offset]);
    }

    /**
     * unset() overloading
     *
     * @param   var offset
     */
    public function offsetUnset($offset) {
      unset($this->h[$offset]);
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
      ob_start();
      com_print_typeinfo($this->h);
      preg_match('/class ([^ ]+) \{ \/\* GUID=([^ ]+) \*\//', ob_get_contents(), $matches);
      ob_end_clean();
      return $this->getClassName().'(->'.get_class($this->h).'<'.$matches[1].'>@'.$matches[2].')';
    }
  }
?>
