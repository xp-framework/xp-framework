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
    public 
      $id = '';
      
    /**
     * Constructor
     * 
     * @access  public
     * @param   string id
     */
    public function __construct($id) {
      $this->id= $id;
      
    }
    
    /**
     * Initialize this storage
     *
     * @model   abstract
     * @access  public
     */
    public abstract function initialize() ;
    
    /**
     * Returns whether this storage contains the given key
     *
     * @model   abstract
     * @access  public
     * @param   string key
     * @return  bool TRUE when this key exists
     */
    public abstract function contains($key) ;
    
    /**
     * Get a key by it's name
     *
     * @model   abstract
     * @access  public
     * @param   string key
     * @return  &mixed
     */
    public abstract function get($key) ;

    /**
     * Insert/update a key
     *
     * @model   abstract
     * @access  public 
     * @param   string key
     * @param   &mixed value
     * @param   int permissions default 0666
     */
    public abstract function put($key, &$value, $permissions= 0666) ;

    /**
     * Remove a key
     *
     * @model   abstract
     * @access  public
     * @param   string key
     */
    public abstract function remove($key) ;

    /**
     * Remove all keys
     *
     * @model   abstract
     * @access  public
     */
    public abstract function free() ;

    /**
     * Get all keys
     *
     * @model   abstract
     * @access  public
     * @return  string[] key
     */
    public abstract function keys() ;

  }
?>
