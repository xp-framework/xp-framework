<?php

/* This class is part of the XP framework
 *
 */
  uses('unittest.mock.arguments.IArgumentMatcher');

 /**
  * Argument matcher that uses a user function for matching.
  *
  * @purpose Argument Matching
  */
  class DynamicMatcher extends Object implements IArgumentMatcher {
    private
      $function= NULL,
      $classOrObject= NULL;
    
    /**
     * Constructor
     * 
     * @param function string
     * @param classOrObject string null
     */
    public function __construct($function, $classOrObject= null) {
      $this->function = $function;
      $this->classOrObject= $classOrObject;
    }
    
    /**
     * Trivial matches implementations.
     * 
     * @param value mixed
     */
    public function matches($value) {
      return call_user_func(array($this->classOrObject, $this->function), $value);
    }
  }
?>