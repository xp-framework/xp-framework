<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Container invocation handler
   *
   * @purpose  invocation handler
   */
  class ContainerInvocationHandler extends Object {
    var
      $container  = NULL;
    
    /**
     * Set container
     *
     * @access  public
     * @param   &remote.server.BeanContainer container
     */
    function setContainer(&$container) {
      $this->container= &$container;
    }

    /**
     * Processes a method invocation on a proxy instance and returns
     * the result.
     *
     * @access  public
     * @param   lang.reflect.Proxy proxy
     * @param   string method the method name
     * @param   mixed args an array of arguments
     * @return  mixed
     */
    function invoke(&$proxy, $method, $args) {

      // TBD Invocation interceptors
      return $this->container->invoke($proxy, $method, $args);
    }

  } implements(__FILE__, 'lang.reflect.InvocationHandler');
?>
