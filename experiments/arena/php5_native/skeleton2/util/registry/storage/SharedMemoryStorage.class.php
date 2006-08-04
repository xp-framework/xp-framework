<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'io.sys.ShmSegment',
    'lang.ElementNotFoundException',
    'util.registry.RegistryStorageProvider'
  );
  
  /**
   * Shared Memory storage
   *
   * @purpose  A storage provider that uses shared memory
   * @see      xp://io.sys.ShmSegment
   */
  class SharedMemoryStorage extends Object implements RegistryStorageProvider {
    public
      $segments = array();
      
    public
      $_seg     = NULL;
    
    /**
     * Initialize this storage
     *
     * @access  public
     * @param   string id
     */
    public function initialize($id) {
      $this->_seg= new ShmSegment($id);
      if (!$this->_seg->isEmpty('segments')) {
        $this->segments= $this->_seg->get('segments');
      }
    }
    
    /**
     * Returns whether this storage contains the given key
     *
     * @access  public
     * @param   string key
     * @return  bool TRUE when this key exists
     */
    public function contains($key) {
      return isset($this->segments[$key]);
    }

    /**
     * Get all keys
     *
     * @access  public
     * @return  string[] key
     */
    public function keys() { 
      return array_keys($this->segments);
    }
    
    /**
     * Get a key by it's name
     *
     * @access  public
     * @param   string key
     * @return  &mixed
     */
    public function &get($key) {
      if (!isset($this->segments[$key])) {
        throw(new ElementNotFoundException($key.' does not exist'));
      }
      
      return $this->segments[$key]->get();
    }

    /**
     * Insert/update a key
     *
     * @access  public 
     * @param   string key
     * @param   &mixed value
     * @param   int permissions default 0666
     */
    public function put($key, &$value, $permissions= 0666) {
      if (!isset($this->segments[$key])) {
        $this->segments[$key]= new ShmSegment($key);
        $this->_seg->put($this->segments);
      }
      return $this->segments[$key]->put($value, $permissions);
    }

    /**
     * Remove a key
     *
     * @access  public
     * @param   string key
     */
    public function remove($key) {
      if (!isset($this->segments[$key])) {
        throw(new ElementNotFoundException($key.' does not exist'));
      }
      
      if (FALSE === $this->segments[$key]->remove()) return FALSE;
      
      unset($this->segments[$key]);
      $this->_seg->put($this->segments);
    }
  
    /**
     * Remove all keys
     *
     * @access  public
     */
    public function free() { 
      $this->segments= array();
      $this->_seg->put($this->segments);
    }
  } 
?>
