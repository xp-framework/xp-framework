<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Invocation strategy
   *
   * @purpose  strategy
   */
  class InvocationStrategy extends Interface {

    /**
     * Invoke a method
     *
     * @access  public
     * @param   &lang.Object instance
     * @param   string method
     * @param   mixed args
     * @return  &mixed
     */
    function &invoke(&$instance, $method, $args) { }
  }
?>
