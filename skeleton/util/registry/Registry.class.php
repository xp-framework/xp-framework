<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Registry is basically a key/value-storage system. This class actually
   * acts as a proxy to the storage providers.
   *
   * Usage (Setup):
   * <code>
   *   $r= &Registry::getInstance(new SharedMemoryStorage('HKEY_GLOBAL'));
   * </code>
   *
   * Usage (somewhere later on):
   * <code>
   *   $r= &Registry::getInstance('HKEY_GLOBAL');
   *   if (!$r->contains('config')) {
   *     $config= &new Properties('foo.ini');
   *     $config->reset();
   *     $r->put('config', $config, 0600);
   *   }
   * </code>
   *
   * @purpose  Provide a mechanism to register any type of variable
   * @see      xp://util.registry.RegistryStorage
   */ 
  class Registry extends Object {
    var
      $storage = NULL;

    /**
     * Return whether a given key exists
     *
     * @access  public
     * @param   string key
     * @return  bool TRUE when the key exists
     */
    function contains($key) {
      return $this->storage->contains($key);
    }
    
    /**
     * Retreive a value by a given key
     *
     * @access  public
     * @param   string key
     * @return  &mixed value
     */
    function &get($key) {
      return $this->storage->read($key);
    }

    /**
     * Insert or update a key/value-pair
     *
     * @access  public
     * @param   string key
     * @param   &mixed value
     * @param   int permissions default 0666
     * @return  bool success
     */
    function put($key, &$value, $permissions= 0666) {
      return $this->storage->write($key, $value, $permissions);
    }

    /**
     * Remove a value by a given key
     *
     * @access  public
     * @param   string key
     * @return  bool success
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
