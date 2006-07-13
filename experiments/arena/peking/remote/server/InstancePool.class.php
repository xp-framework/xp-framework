<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Hashmap');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class InstancePool extends Object {
    var
      $_pool    = NULL,
      $_h2id    = NULL;

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function __construct() {
      $this->_pool= &new Hashmap();
    }
      
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function registerInstance(&$object) {
      $this->_pool->putref($object->hashCode(), $object);
      return TRUE;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function fetch($hashCode) {
      return $this->_pool->get($hashCode);
    }    
  }
?>
