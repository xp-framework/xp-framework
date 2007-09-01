<?php
/* This class is part of the XP framework
 *
 * $Id: InvocationHandler.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace lang::reflect;

  /**
   * InvocationHandle is the interface implemented by
   * the invocation handler of a proxy instance.
   *
   * Each proxy instance has an associated invocation handler.
   * When a method is invoked on a proxy instance, the method
   * invocation is encoded and dispatched to the invoke
   * method of its invocation handler.
   *
   * @see      xp://lang.reflect.Proxy
   * @purpose  Interface
   */
  interface InvocationHandler {
  
    /**
     * Processes a method invocation on a proxy instance and returns
     * the result.
     *
     * @param   lang.reflect.Proxy proxy
     * @param   string method the method name
     * @param   mixed* args an array of arguments
     * @return  mixed
     */
    public function invoke($proxy, $method, $args);
  
  }
?>
