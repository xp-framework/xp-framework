<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('io.sys.ShmSegment', 'util.registry.storage.RegistryStorage');
  
  /**
   * Shared Memory storage
   *
   * @purpose  A storage provider that uses shared memory
   * @see      xp://io.sys.ShmSegment
   */
  class SharedMemoryStorage extends RegistryStorage {
    var
      $segments = array();
      
    var
      $_seg     = NULL;
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function initialize() {
      $this->_seg= &new ShmSegment($this->id);
      if (!$this->_seg->isEmpty('segments')) {
        $this->segments= $this->_seg->get('segments');
      }
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function contains($key) {
      return isset($this->segments[$key]);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function &get($key) {
      if (!isset($this->segments[$key])) {
        return throw(new ElementNotFoundException($key.' does not exist'));
      }
      
      return $this->segments[$key]->get();
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function put($key, &$value, $permissions= 0666) {
      if (!isset($this->segments[$key])) {
        $this->segments[$key]= &new ShmSegment($key);
      }
      return $this->segments[$key]->put($value, $permissions);
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function remove($key) {
      if (!isset($this->segments[$key])) {
        return throw(new ElementNotFoundException($key.' does not exist'));
      }
      
      if (FALSE === $this->segments[$key]->remove()) return FALSE;
      
      unset($this->segments[$key]);
      $this->_seg->put($this->segments);
    }
  
  }
?>
