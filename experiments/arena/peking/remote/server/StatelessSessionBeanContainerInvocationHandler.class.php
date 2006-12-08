<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Invocation handler for stateless
   * session beans
   *
   * @purpose  invocationhandler
   */
  class StatelessSessionBeanContainerInvocationHandler extends Object {
    var
      $container  = NULL,
      $type       = NULL;
    
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
     * Set type
     *
     * @access  public
     * @param   int type
     */
    function setType($type) {
      $this->type= $type;
    }

    /**
     * Processes a method invocation on a proxy instance and returns
     * the result.
     *
     * @access  public
     * @param   &lang.reflect.Proxy proxy
     * @param   string method the method name
     * @param   mixed args an array of arguments
     * @return  mixed
     */
    function invoke(&$proxy, $method, $args) {
      return $this->container->invoke($method, $args);
    }

  } implements(__FILE__, 'lang.reflect.InvocationHandler');
?>
