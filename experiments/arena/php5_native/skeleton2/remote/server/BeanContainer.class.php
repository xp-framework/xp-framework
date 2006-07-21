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
    public
      $peer         = NULL,
      $container    = NULL,
      $strategy     = NULL;

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function &forClass(&$class) {
      $bc= &new BeanContainer();
      $bc->instancePool= &Collection::forClass($class);
      return $bc;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function setContainerID($cid) {
      $this->cid= $cid;
    }    
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function setPeer(&$peer) {
      $this->peer= &$peer;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function setInvocationStrategy(&$strategy) {
      $this->strategy= &$strategy;
    }
      
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function invokeHome($method, $args) {
      return $this->strategy->invokeHome($this, $method, $args);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function invoke($oid, $method, $args) {
      return $this->strategy->invoke($this, $oid, $method, $args);
    }
  }
?>
