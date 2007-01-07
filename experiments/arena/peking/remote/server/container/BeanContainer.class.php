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
    public
      $instancePool = NULL;

    /**
     * Get instance for class
     *
     * @param   lang.XPClass class
     * @return  remote.server.BeanContainer
     */
    public function forClass($class) {
      $bc= new BeanContainer();
      $bc->instancePool= Collection::forClass($class->getName());
      return $bc;
    }

    /**
     * Invoke a method
     *
     * @param   lang.Object proxy
     * @param   string method
     * @param   mixed args
     * @return  mixed
     */
    public function invoke($proxy, $method, $args) { }
  }
?>
