<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'lang.System',
    'io.File',
    'io.FileUtil',
    'util.Hashmap',
    'util.registry.storage.RegistryStorage'
  );

  /**
   * DBA storage
   *
   * @purpose  A storage provider that uses flat files
   * @see      php://serialize
   */
  class FlatfileStorage extends RegistryStorage {
    protected
      $_file   = NULL,
      $_hash   = NULL;

    /**
     * Initialize this storage
     *
     * @access  public
     */
    public function initialize() {
      $this->_file= new File(System::tempDir().DIRECTORY_SEPARATOR.$this->id.'.dat');
      if (!$this->_file->exists()) {
        touch($this->_file->getURI());
        $this->_hash= new Hashmap();
      } else {
        $this->_hash= unserialize(FileUtil::getContents($this->_file));
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
      return $this->_hash->containsKey($key);
    }

    /**
     * Get all keys
     *
     * @access  public
     * @return  string[] key
     */
    public function keys() { 
      return $this->_hash->keys();
    }
    
    /**
     * Get a key by it's name
     *
     * @access  public
     * @param   string key
     * @return  &mixed
     */
    public function get($key) {
      return $this->_hash->get($key);
    }

    /**
     * Insert/update a key
     *
     * @access  public 
     * @param   string key
     * @param   &mixed value
     * @param   int permissions default 0666 (ignored for this storage)
     */
    public function put($key, $value, $permissions= 0666) {
      $this->_hash->put($key, $value);
      FileUtil::setContents($this->_file, serialize($this->_hash));
    }

    /**
     * Remove a key
     *
     * @access  public
     * @param   string key
     */
    public function remove($key) {
      $this->_hash->remove($key);
      FileUtil::setContents($this->_file, serialize($this->_hash));
    }
  
    /**
     * Remove all keys
     *
     * @access  public
     */
    public function free() { 
      $this->_hash->clear();
      FileUtil::setContents($this->_file, serialize($this->_hash));
    }
  }
?>
