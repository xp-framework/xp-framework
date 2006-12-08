<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'remote.reflect.InterfaceUtil',
    'lang.reflect.Proxy'
  );

  /**
   * Stateless session invocation strategy
   *
   * @purpose  strategy
   */
  class StatelessSessionInvocationStrategy extends Object {
    var
      $poolSize = 1;

    /**
     * Invoke a method
     *
     * @access  public
     * @param   &lang.Object instance
     * @param   string method
     * @param   mixed args
     * @return  &mixed
     */
    function &invoke(&$instance, $method, $args) {
      if (!$method) return FALSE;
      
      $ret= $method->invoke($instance, $args);
      return $ret;
    }
  } implements(__FILE__, 'remote.server.strategy.InvocationStrategy');
?>
