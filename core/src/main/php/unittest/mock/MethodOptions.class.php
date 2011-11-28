<?php

/* This class is part of the XP framework
 *
 */

  uses('unittest.mock.IMethodOptions',
       'unittest.mock.Expectation');

  /**
   * Implements a fluent interface for specifying mock expectation.
   *
   * @purpose Mocking
   */
  class MethodOptions extends Object implements IMethodOptions {
    private
      $expectation= null,
      $methodName= null;

    /**
     * Constructor
     *
     * @param Array expectation
     */
    public function  __construct($expectation, $methodName) {
      if(!($expectation instanceof Expectation))
        throw new IllegalArgumentException('Invalid expectation map passed.');

      if(!($methodName))
        throw new IllegalArgumentException('Method name required.');
            
      $this->expectation= $expectation;
      $this->methodName= $methodName;
    }
    
    /**
     * Specifies the expected return value.
     *
     * @param  var The value that is to be returned on a method call.
     * @return IMethodOptions
     */
    public function returns($value) {
      $this->expectation->setReturn($value);
      return $this;
    }

    /**
     * Specifies the exception that is to be thrown.
     *
     * @param  lang.Throwable the exception that is to be thrown on a method call.
     * @return IMethodOptions
     */
    public function throws(Throwable $exception) {
      $this->expectation->setException($exception);
      return $this;
    }

    /**
     * Specifies the number of calls that are expected for the method.
     *
     * @param int repeatCount
     * @return IMethodOptions
     */
    public function repeat($repeatCount) {
      $this->expectation->setRepeat($repeatCount);
      return $this;
    }
    
    /**
     * Specifies that this expection is valid for all calls the method.
     * 
     * @return IMethodOptions
     */
    public function repeatAny() {
      return $this->repeat(-1);
    }
    
    public function propertyBehavior() {
      $prefix= substr($this->methodName, 0, 3);
      if($prefix != 'set' && $prefix != 'get')
        throw new IllegalStateException('Property behavior is only applicable to getters and setters.');
      
      $this->expectation->setPropertyBehavior();
      return $this;
    }
  }
?>