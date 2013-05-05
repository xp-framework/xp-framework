<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.DeferredInitializationException', 'lang.reflect.InvocationHandler');

  /**
   * Lazy initializable InvokationHandler 
   *
   * @test  xp://net.xp_framework.unittest.util.DeferredInvokationHandlerTest
   */
  abstract class AbstractDeferredInvokationHandler extends Object implements InvocationHandler {
    private $_instance= NULL;

    /**
     * Lazy initialization callback
     *
     * @return  lang.Generic
     */
    public abstract function initialize();

    /**
     * Processes a method invocation on a proxy instance and returns
     * the result.
     *
     * @param   lang.reflect.Proxy proxy
     * @param   string method the method name
     * @param   var* args an array of arguments
     * @return  var
     * @throws  util.DeferredInitializationException
     */
    public function invoke($proxy, $method, $args) {
      if (NULL === $this->_instance) {
        try {
          $this->_instance= $this->initialize();
        } catch (Throwable $e) {
          $this->_instance= NULL;
          throw new DeferredInitializationException($method, $e);
        }
        if (!$this->_instance instanceof Generic) {
          throw new DeferredInitializationException(
            $method,
            XPClass::forName('lang.ClassCastException')->newInstance(
              'Initializer returned '.xp::typeOf($this->_instance)
            )
          );
        }
      }
      return call_user_func_array(array($this->_instance, $method), $args);
    }
  } 
?>
