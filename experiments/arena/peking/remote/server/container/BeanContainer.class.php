<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.Collection');

  /**
   * Bean container
   *
   * @purpose  abstract baseclass
   */
  class BeanContainer extends Object {
    var
      $instancePool = NULL;

    /**
     * Get instance for class
     *
     * @access  public
     * @param   &lang.XPClass class
     * @return  &remote.server.BeanContainer
     */
    function &forClass(&$class) {
      $bc= &new BeanContainer();
      $bc->instancePool= &Collection::forClass($class->getName());
      return $bc;
    }

    /**
     * Invoke a method
     *
     * @access  public
     * @param   &lang.Object proxy
     * @param   string method
     * @param   mixed args
     * @return  mixed
     */
    function invoke(&$proxy, $method, $args) { }
  }
?>
