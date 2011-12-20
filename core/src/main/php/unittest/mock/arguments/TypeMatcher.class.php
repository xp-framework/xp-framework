<?php

/* This class is part of the XP framework
 *
 */
  uses('unittest.mock.arguments.IArgumentMatcher',
       'lang.reflect.InvocationHandler');

 /**
  * Trivial argument matcher, that just returns true.
  *
  * @purpose Argument Matching
  */
  class TypeMatcher extends Object implements IArgumentMatcher, InvocationHandler  {
    private 
      $type,
      $matchNull;
    
    /**
     * Constructor.
     * 
     * @param value string
     */
    public function __construct($type, $matchNull= TRUE) {
      $this->type= $type;
      $this->matchNull= $matchNull;
    }
    
    /**
     * Trivial matches implementations.
     * 
     * @param value mixed
     */
    public function matches($value) {
      if(NULL === $value && $this->matchNull) {
        return TRUE;
      }
      
      return xp::typeof($value) == XPClass::forName($this->type)->getName();
    }

    public function invoke($proxy, $method, $args) {
      if($method == 'matches') {
        return $this->matches($args[0]);
      }
      
      throw new IllegalStateException('Unknown method "'.$method.'".');
    }
  }
?>