<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.invoke.InvocationInterceptor');

  /**
   * Handles interceptor invocations
   *
   * @test     xp://tests.InvocationChainTest
   * @purpose  Chain
   */
  class InvocationChain extends Object {
    public
      $instance   = NULL,
      $method     = NULL,
      $parameters = array(),
      $context    = array();

    public
      $_calls     = array(),
      $_offset    = 0;
    
    /**
     * Add an interceptor
     *
     * @param   de.schlund.intranet.search.interceptor.InvocationInterceptor instance
     */
    public function addInterceptor(InvocationInterceptor $instance) {
      $this->_calls[]= array($instance, $instance->getClass()->getMethod('invoke'));
    }
    
    /**
     * Set context data for a given key
     *
     * @param   string key
     * @param   mixed val
     */
    public function setContextData($key, $val) {
      $this->context[$key]= $val;
    }

    /**
     * Retrieve context data by a given key
     *
     * @param   string key
     * @return  mixed
     */
    public function getContextData($key) {
      if (array_key_exists($key, $this->context)) {
        return $this->context[$key];
      }
      return NULL;
    }

    /**
     * Invoke a call by a given offset
     *
     * @param   int i
     */
    public function invokeCall($i) {
      return $this->_calls[$i][1]->invoke($this->_calls[$i][0], array($this));
    }

    /**
     * Start chain for given instance and method
     *
     * @param   lang.Object instance
     * @param   lang.reflect.Method method
     * @param   mixed[] parameters
     * @return  mixed
     */
    public function invoke($instance, $method, $parameters) {
      $this->_offset= 0;
      $this->instance= $instance;
      $this->method= $method;
      $this->parameters= $parameters;

      return $this->proceed();
    }

    /**
     * Proceed with chain
     *
     */
    public function proceed() {
      if ($this->_offset >= sizeof($this->_calls)) {
        // We're at the end of the chain
        return $this->method->invoke($this->instance, $this->parameters);
      }

      try {
        return $this->invokeCall($this->_offset++);
      } catch (Throwable $e) {
        $this->_offset= sizeof($this->_calls)+ 1;   // Make chain stop here
        throw $e;
      }
    }
  }
?>
