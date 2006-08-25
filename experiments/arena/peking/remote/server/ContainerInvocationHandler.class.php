<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class ContainerInvocationHandler extends Object {
    var
      $container  = NULL;
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
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
     * @param   mixed* args an array of arguments
     * @return  mixed
     */
    function invoke(&$proxy, $method, $args) {

      // TBI: Invocation interceptors
      return $this->container->invoke($proxy, $method, $args);
    }

  } implements(__FILE__, 'lang.reflect.InvocationHandler');
?>
