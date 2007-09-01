<?php
/* This class is part of the XP framework
 *
 * $Id: StatelessSessionBeanContainerInvocationHandler.class.php 9151 2007-01-07 13:52:49Z kiesel $ 
 */

  namespace remote::server;

  uses('lang.reflect.InvocationHandler');

  /**
   * Invocation handler for stateless
   * session beans
   *
   * @purpose  invocationhandler
   */
  class StatelessSessionBeanContainerInvocationHandler extends lang::Object implements lang::reflect::InvocationHandler {
    public
      $container  = NULL,
      $type       = NULL;
    
    /**
     * Set container
     *
     * @param   remote.server.BeanContainer container
     */
    public function setContainer($container) {
      $this->container= $container;
    }
    
    /**
     * Set type
     *
     * @param   int type
     */
    public function setType($type) {
      $this->type= $type;
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
      return $this->container->invoke($method, $args);
    }

  } 
?>
