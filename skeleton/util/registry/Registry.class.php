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
   * $r= &Registry::getInstance(new SharedMemoryStorage('global'));
   *
   * // Somewhere later on
   * $r= &Registry::getInstance('global');
   * if (!$r->contains('config')) {
   *   $config= &new Properties('foo.ini');
   *   $config->reset();
   *   $r->put('config', $config, 0600);
   * }
   * </code>
   */ 
  class Registry extends Object {
    var
      $storage = NULL;

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
     * @param   mixed a string or a util.registry.RegistryStorage object
     * @return  &util.Registry registry object
     * @throws  IllegalArgumentException
     */
    function &getInstance() {
      static $__instance = array();
      
      $p= &func_get_arg(0);
      
      // Subsequent calls
      if (is_string($p)) {
        if (!isset($__instance[$p])) {
          return throw(new IllegalAccessException('Registry "'.$p.'" hasn\'t been setup yet'));
        }
        return $__instance[$p];
      }
      
      // Initial setup
      if (is_a($p, 'RegistryStorage')) {
        
        $__instance[$storage->id]= new Registry();
        $__instance[$storage->id]->storage= &$p;
        $__instance[$storage->id]->storage->initialize();
        
        return $__instance[$storage->id];
      }
      
      trigger_error('Type: '.gettype($p), E_USER_WARNING);
      return throw(new IllegalArgumentException('Argument passed is of wrong type'));
    }
  }
?>
