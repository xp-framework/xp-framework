<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Registry
   *
   * Usage: Setup
   * <code>
   * $r= &Registry::getInstance();
   * $r->setStorage(new SharedMemoryStorage());
   * </code>
   *
   * Usage: Registering a value
   * <code>
   * $r= &Registry::getInstance();
   * if (!$r->contains('config')) {
   *   $config= &new Properties('foo.ini');
   *   $config->reset();
   *   $r->put('config', $config, 0600);
   * }
   * </code>
   */ 
  class Registry extends Object {
    var
      $storage= NULL;

    /**
     * Set the storage
     *
     * @access  public
     * @param   &util.registry.RegistryStorage
     * @throws  IllegalArgumentException
     */  
    function setStorage(&$storage) {
      if (!is_a($storage, 'RegistryStorage')) {
        return throw(new IllegalArgumentException('Argument storage is not a RegistryStorage object'));
      }
      $this->storage= &$storage;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function contains($key) {
      return $this->storage->contains($key);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function &get($key) {
      return $this->storage->read($key);
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function put($key, &$value, $permissions= 0666) {
      return $this->storage->write($key, $value, $permissions);
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function remove($key) {
      return $this->storage->delete($key);
    }

    /**
     * Get an instance
     * 
     * @access  static
     * @return  &util.Registry registry object
     */
    function &getInstance() {
      static $__instance;
      
      if (!isset($__instance)) {
        $__instance= new Registry();
      }
      return $__instance;
    }
  }
?>
