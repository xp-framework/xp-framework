<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('lang.ElementNotFoundException');


  /**
   * RegistryStorage
   *
   * @purpose  Abstract base class for storage
   * @see      xp://util.registry.Registry
   */
  class RegistryStorage extends Object {
    var 
      $id = '';
      
    /**
     * Constructor
     * 
     * @access  public
     * @param   string id
     */
    function __construct($id) {
      $this->id= $id;
      parent::__construct();
    }
    
    /**
     * Initialize this storage
     *
     * @access  public
     */
    function initialize() { }
    
    /**
     * Returns whether this storage contains the given key
     *
     * @access  public
     * @param   string key
     * @return  bool TRUE when this key exists
     */
    function contains($key) { }
    
    /**
     * Get a key by it's name
     *
     * @access  public
     * @param   string key
     * @return  &mixed
     */
    function &get($key) { }

    /**
     * Insert/update a key
     *
     * @access  public 
     * @param   string key
     * @param   &mixed value
     * @param   int permissions default 0666
     */
    function put($key, &$value, $permissions= 0666) { }

    /**
     * Remove a key
     *
     * @access  public
     * @param   string key
     */
    function remove($key) { }

    /**
     * Get all keys
     *
     * @access  public
     * @return  string[] key
     */
    function keys() { }

  }
?>
