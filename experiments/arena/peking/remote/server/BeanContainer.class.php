<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.Collection');

  /**
   * Bean container
   *
   * @purpose  container
   */
  class BeanContainer extends Object {
    var
      $container    = NULL,
      $strategy     = NULL;

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
     * Set container id
     *
     * @access  public
     * @param   int cid
     */
    function setContainerID($cid) {
      $this->cid= $cid;
    }    
    
    /**
     * Set strategy
     *
     * @access  public
     * @param   &remote.server.strategy.InvocationStrategy strategy
     */
    function setInvocationStrategy(&$strategy) {
      $this->strategy= &$strategy;
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
      if (!$this->instancePool->contains($oid)) {
        $instance= &$class->newInstance();
        $this->instancePool->add($instance);
      } else {
        $instance= &$this->instancePool->get($oid);
      }

      $m= &$class->getMethod($method);
      
      $log= &Logger::getInstance();
      $cat= &$log->getCategory($this->getClassName());
      $cat->debug('BeanContainer::invoke(', $oid, ') ', $m->toString(), '(', $args, ')');

      return $m->invoke($instance, $args);
    }
  }
?>
