<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.Hashmap');
 
  /**
   * Memory storage
   *
   * @purpose  A storage provider
   */
  class MemoryStorage extends Object {
    var
      $_hash   = NULL;
    
    /**
     * Initialize this storage
     *
     * @access  public
     * @param   string id
     */
    function initialize($id) {
      $this->_hash= &new Hashmap();
    }

    /**
     * Returns whether this storage contains the given key
     *
     * @access  public
     * @param   string key
     * @return  bool TRUE when this key exists
     */
    function contains($key) {
      return $this->_hash->containsKey($key);
    }

    /**
     * Get all keys
     *
     * @access  public
     * @return  string[] key
     */
    function keys() { 
      return $this->_hash->keys();
    }
    
    /**
     * Get a key by it's name
     *
     * @access  public
     * @param   string key
     * @return  &mixed
     */
    function &get($key) {
      return $this->_hash->get($key);
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
      $this->_hash->put($key, $value);
    }

    /**
     * Remove a key
     *
     * @access  public
     * @param   string key
     */
    function remove($key) {
      $this->_hash->remove($key);
    }
  
    /**
     * Remove all keys
     *
     * @access  public
     */
    function free() { 
      $this->_hash->clear();
    }
  } implements(__FILE__, 'util.registry.RegistryStorageProvider');
?>
