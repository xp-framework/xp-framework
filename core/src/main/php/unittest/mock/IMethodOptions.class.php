<?php

  /* This interface is part of the XP framework
   *
   * $Id$
   */
  uses('lang.Throwable');
  /**
   * Fluent interface for specifying mock expectation.
   *
   * @purpose  Mockery
   */
  interface IMethodOptions {
    
    /**
     * Specifies return value for that method call.
     *
     * @param   mixed The return value for that expectation.
     * @return  IMethodOptions
     */
    function returns($value);

    /**
     * Specifies an exception that should be thrown on exectution of that method.
     *
     * @param   lang.Throwable The exception that should be thrown
     * @return  IMethodOptions
     */
    function throws(Throwable $exception);
    /**
     * Specifies the number of returns for that method. -1 for unlimited.
     *
     * @param int
     */
    function repeat($count);

    /**
     * Specifies that that method may be called an unlimited number of times.
     */
    function repeatAny();
  }
?>