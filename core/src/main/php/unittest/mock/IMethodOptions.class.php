<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.Throwable');
  
  /**
   * Fluent interface for specifying mock expectation.
   *
   */
  interface IMethodOptions {
    
    /**
     * Specifies return value for that method call.
     *
     * @param   mixed The return value for that expectation.
     * @return  IMethodOptions
     */
    public function returns($value);

    /**
     * Specifies an exception that should be thrown on exectution of that method.
     *
     * @param   lang.Throwable The exception that should be thrown
     * @return  IMethodOptions
     */
    public function throws(Throwable $exception);
    
    /**
     * Specifies the number of returns for that method. -1 for unlimited.
     *
     * @param int
     */
    public function repeat($count);

    /**
     * Specifies that that method may be called an unlimited number of times.
     */
    public function repeatAny();
    
    /**
     * Specifies that the given getter/setter are to be treated as property 
     * getter/setter. 
     */
    public function propertyBehavior();
  }
?>
