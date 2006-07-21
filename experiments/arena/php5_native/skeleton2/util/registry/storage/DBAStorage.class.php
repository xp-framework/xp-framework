<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'lang.System',
    'io.dba.DBAFile'
  );
  
  /**
   * DBA storage
   *
   * @purpose  A storage provider that uses the db abstraction layer
   * @see      xp://io.dba.DBAFile
   */
  class DBAStorage extends Object implements RegistryStorageProvider {
    public
      $handler = '';

    public
      $_db     = NULL;

    /**
     * Constructor
     * 
     * @access  public
     * @param   string handler default DBH_GDBM one of the DBH_* constants
     * @see     xp://io.dba.DBAFile (section "class constants")
     */
    public function __construct($handler= DBH_GDBM) {
      $this->handler= $handler;
    }
    
    /**
     * Initialize this storage
     *
     * @access  public
     * @param   string id
     */
    public function initialize($id) {
      $this->_db= &new DBAFile(
        System::tempDir().DIRECTORY_SEPARATOR.$id.'.db', 
        $this->handler
      );
      
      // See php://dba_open: Use "c" for read/write access and database 
      // creation if it doesn't currently exist
      $this->_db->open(DBO_CREATE);
    }
    
    /**
     * Returns whether this storage contains the given key
     *
     * @access  public
     * @param   string key
     * @return  bool TRUE when this key exists
     */
    public function contains($key) {
      return $this->_db->lookup($key);
    }

    /**
     * Get all keys
     *
     * @access  public
     * @return  string[] key
     */
    public function keys() { 
      return $this->_db->keys();
    }
    
    /**
     * Get a key by it's name
     *
     * @access  public
     * @param   string key
     * @return  &mixed
     */
    public function &get($key) {
      return $this->_db->fetch($key);
    }

    /**
     * Insert/update a key
     *
     * @access  public 
     * @param   string key
     * @param   &mixed value
     * @param   int permissions default 0666 (ignored for this storage)
     */
    public function put($key, &$value, $permissions= 0666) {
      $this->_db->store($key, $value);
    }

    /**
     * Remove a key
     *
     * @access  public
     * @param   string key
     */
    public function remove($key) {
      $this->_db->delete($key);
    }
  
    /**
     * Remove all keys
     *
     * @access  public
     */
    public function free() { 
      for ($i= &$this->_db->iterator(); $i->hasNext(); ) {
        $this->_db->delete($i->next());
      }
    }
  } 
?>
