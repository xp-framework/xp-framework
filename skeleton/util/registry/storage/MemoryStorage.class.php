<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('util.registry.storage.RegistryStorage');
  
  /**
   * Memory storage
   *
   * @purpose  A storage provider
   */
  class MemoryStorage extends RegistryStorage {
    var
      $segments = array();
    
    /**
     * Returns whether this storage contains the given key
     *
     * @access  public
     * @param   string key
     * @return  bool TRUE when this key exists
     */
    function contains($key) {
      return isset($this->segments[$key]);
    }

    /**
     * Get all keys
     *
     * @access  public
     * @return  string[] key
     */
    function keys() { 
      return array_keys($this->segments);
    }
    
    /**
     * Get a key by it's name
     *
     * @access  public
     * @param   string key
     * @return  &mixed
     */
    function &get($key) {
      if (!isset($this->segments[$key])) {
        return throw(new ElementNotFoundException($key.' does not exist'));
      }
      
      return $this->segments[$key]->get();
    }

    /**
     * Insert/update a key
     *
     * @access  public 
     * @param   string key
     * @param   &mixed value
     * @param   int permissions default 0666 (ignored)
     */
    function put($key, &$value, $permissions= 0666) {
      $this->segments[$key]= &$value;
    }

    /**
     * Remove a key
     *
     * @access  public
     * @param   string key
     */
    function remove($key) {
      unset($this->segments[$key]);
    }
  
    /**
     * Remove all keys
     *
     * @access  public
     */
    function free() { 
      $this->segments= array();
    }
  }
?>
