<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'remote.server.container.BeanContainer',
    'lang.Collection'
  );

  /**
   * Bean container
   *
   * @purpose  abstract baseclass
   */
  class StatelessSessionBeanContainer extends BeanContainer {

    /**
     * Get instance for class
     *
     * @access  public
     * @param   &lang.XPClass class
     * @return  &remote.server.BeanContainer
     */
    function &forClass(&$class) {
      $bc= &new StatelessSessionBeanContainer();
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
    function invoke(&$proxy, $method, $args) {
      $class= &$this->instancePool->getElementClass();

      if ($this->instancePool->isEmpty()) {
        $instance= &$class->newInstance();
        $this->instancePool->add($instance);
      } else {
        $instance= &$this->instancePool->get(0);
      }

      $m= &$class->getMethod($method);
      $l= &Logger::getInstance();
      $this->cat= &$l->getCategory();
      $this->cat && $this->cat->debug('BeanContainer::invoke() ', $m->toString(), '(', $args, ')');

      return $m->invoke($instance, $args);
    }
  }
?>
