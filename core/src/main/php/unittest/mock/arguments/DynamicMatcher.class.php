<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.mock.arguments.IArgumentMatcher');

  /**
   * Argument matcher that uses a user function for matching.
   *
   */
  class DynamicMatcher extends Object implements IArgumentMatcher {
    private
      $function      = NULL,
      $classOrObject = NULL;
    
    /**
     * Constructor
     * 
     * @param   string function
     * @param   var classOrObject
     */
    public function __construct($function, $classOrObject= NULL) {
      $this->function= $function;
      $this->classOrObject= $classOrObject;
    }
    
    /**
     * Trivial matches implementations.
     * 
     * @param   var value
     * @return  bool
     */
    public function matches($value) {
      return call_user_func(array($this->classOrObject, $this->function), $value);
    }
  }
?>
