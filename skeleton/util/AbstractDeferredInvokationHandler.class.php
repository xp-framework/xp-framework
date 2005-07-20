<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Lazy initializable InvokationHandler 
   *
   * @purpose  proxy
   */
  class AbstractDeferredInvokationHandler extends Object {
    var
      $_instance = NULL;

    /**
     * Lazy initialization callback
     *
     * @model   abstract
     * @access  protected
     * @return  &lang.Object
     */
    function &initialize() { }

    /**
     * Processes a method invocation on a proxy instance and returns
     * the result.
     *
     * @access  public
     * @param   lang.reflect.Proxy proxy
     * @param   string method the method name
     * @param   mixed* args an array of arguments
     * @return  mixed
     * @throws  util.DeferredInitializationException
     */
    function invoke(&$proxy, $method, $args) {
      if (!isset($this->_instance)) {
        try(); {
          $this->_instance= &$this->initialize();
        } if (catch('Throwable', $e)) {
          $this->_instance= NULL;
          return throw(new DeferredInitializationException($method, $e));
        }
      }
      return call_user_func_array(array(&$this->_instance, $method), $args);
    }
    
  } implements(__FILE__, 'lang.reflect.InvocationHandler');
?>
