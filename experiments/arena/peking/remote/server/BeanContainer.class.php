<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.Collection');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class BeanContainer extends Object {
    var
      $container    = NULL,
      $strategy     = NULL;

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function &forClass(&$class) {
      $bc= &new BeanContainer();
      $bc->instancePool= &Collection::forClass($class->getName());
      return $bc;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setContainerID($cid) {
      $this->cid= $cid;
    }    
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setInvocationStrategy(&$strategy) {
      $this->strategy= &$strategy;
    }
      
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
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
