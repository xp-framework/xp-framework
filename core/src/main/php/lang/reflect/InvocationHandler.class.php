<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

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
     * @param   var* args an array of arguments
     * @return  var
     */
    public function invoke($proxy, $method, $args);
  
  }
?>
