<?php
/* This class is part of the XP framework
 *
 * $Id: AbstractDeferredInvokationHandler.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace util;

  ::uses('util.DeferredInitializationException', 'lang.reflect.InvocationHandler');

  /**
   * Lazy initializable InvokationHandler 
   *
   * @purpose  proxy
   */
  class AbstractDeferredInvokationHandler extends lang::Object implements lang::reflect::InvocationHandler {
    public
      $_instance = NULL;

    /**
     * Lazy initialization callback
     *
     * @return  lang.Object
     */
    public function initialize() { }

    /**
     * Processes a method invocation on a proxy instance and returns
     * the result.
     *
     * @param   lang.reflect.Proxy proxy
     * @param   string method the method name
     * @param   mixed* args an array of arguments
     * @return  mixed
     * @throws  util.DeferredInitializationException
     */
    public function invoke($proxy, $method, $args) {
      if (!isset($this->_instance)) {
        try {
          $this->_instance= $this->initialize();
        } catch (lang::Throwable $e) {
          $this->_instance= NULL;
          throw(new DeferredInitializationException($method, $e));
        }
      }
      return call_user_func_array(array($this->_instance, $method), $args);
    }
    
  } 
?>
