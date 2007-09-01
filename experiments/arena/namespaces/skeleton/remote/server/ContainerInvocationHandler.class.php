<?php
/* This class is part of the XP framework
 *
 * $Id: ContainerInvocationHandler.class.php 9151 2007-01-07 13:52:49Z kiesel $ 
 */

  namespace remote::server;

  uses('lang.reflect.InvocationHandler');

  /**
   * Container invocation handler
   *
   * @purpose  invocation handler
   */
  class ContainerInvocationHandler extends lang::Object implements lang::reflect::InvocationHandler {
    public
      $container  = NULL;
    
    /**
     * Set container
     *
     * @param   remote.server.BeanContainer container
     */
    public function setContainer($container) {
      $this->container= $container;
    }

    /**
     * Processes a method invocation on a proxy instance and returns
     * the result.
     *
     * @param   lang.reflect.Proxy proxy
     * @param   string method the method name
     * @param   mixed args an array of arguments
     * @return  mixed
     */
    public function invoke($proxy, $method, $args) {

      // TBD Invocation interceptors
      return $this->container->invoke($proxy, $method, $args);
    }

  } 
?>
