<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  /**
   * RegistryStorageProvider
   *
   * @purpose  Interface
   * @see      xp://util.registry.Registry
   */
  class RegistryStorageProvider extends Interface {
      
    /**
     * Initialize this storage
     *
     * @model   abstract
     * @param   string name
     * @access  public
     */
    function initialize($name) { }
    
    /**
     * Returns whether this storage contains the given key
     *
     * @model   abstract
     * @access  public
     * @param   string key
     * @return  bool TRUE when this key exists
     */
    function contains($key) { }
    
    /**
     * Get a key by it's name
     *
     * @model   abstract
     * @access  public
     * @param   string key
     * @return  &mixed
     */
    function &get($key) { }

    /**
     * Insert/update a key
     *
     * @model   abstract
     * @access  public 
     * @param   string key
     * @param   &mixed value
     * @param   int permissions default 0666
     */
    function put($key, &$value, $permissions= 0666) { }

    /**
     * Remove a key
     *
     * @model   abstract
     * @access  public
     * @param   string key
     */
    function remove($key) { }

    /**
     * Remove all keys
     *
     * @model   abstract
     * @access  public
     */
    function free() { }

    /**
     * Get all keys
     *
     * @model   abstract
     * @access  public
     * @return  string[] key
     */
    function keys() { }

  }
?>
