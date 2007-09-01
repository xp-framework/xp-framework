<?php
/* This class is part of the XP framework
 *
 * $Id: DBAFile.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace io::dba;

  ::uses('io.IOException', 'io.dba.DBAIterator');

  // Open modes
  define('DBO_READ',    'r');
  define('DBO_WRITE',   'w');
  define('DBO_CREATE',  'c');
  define('DBO_TRUNC',   'n');
  
  // Handlers
  define('DBH_GDBM',     'gdbm');
  define('DBH_NDBM',     'ndbm');
  define('DBH_DBM',      'dbm');
  define('DBH_DB2',      'db2');
  define('DBH_DB3',      'db3');
  define('DBH_DB4',      'db4');
  define('DBH_CDB',      'cdb');
  define('DBH_CDBMAKE',  'cdb_make');
  define('DBH_FLATFILE', 'flatfile');
  define('DBH_INIFILE',  'inifile');

  /**
   * DBA File - abstraction layer of Berkeley DB style databases. Wraps
   * GDBM, NDBM, DBM, DB2, ... into one API.
   *
   * Usage example (dumping the contents of a GDBM database):
   * <code>
   *   uses('io.dba.DBAFile');
   *   
   *   $db= &new DBAFile('test.gdbm', DBH_GDBM);
   *   $db->open(DBO_READ);
   *   for ($i= &$db->iterator(); $i->hasNext(); ) {
   *     $key= $i->next();
   *     printf("%-30s => %s\n", $key, $db->fetch($key));
   *   }
   *   
   *   $db->close();
   * </code>
   *
   * Usage example (storing a value in a CDB database):
   * <code>
   *   uses('io.dba.DBAFile');
   *   
   *   $db= &new DBAFile('test.cdb', DBH_CDB);
   *   $db->open(DBO_CREATE);
   *   $db->store('path', ini_get('include_path'));
   *   $db->save($optimize= TRUE);
   *   $db->close();
   * </code>
   *
   * @ext      dba
   * @see      php://dba
   * @purpose  Access Berkeley DB style databases.
   */
  class DBAFile extends lang::Object {
    public
      $filename = '',
      $handler  = '';

    public
      $_fd      = NULL;
      
    /**
     * Constructor
     *
     * @param   string filename
     * @param   string handler one of DBH_* handler constants
     * @see     php://dba#dba.requirements Handler decriptions
     */
    public function __construct($filename, $handler) {
      $this->filename= $filename;
      $this->handler= $handler;
    }

    /**
     * Get Filename
     *
     * @return  string
     */
    public function getFilename() {
      return $this->filename;
    }

    /**
     * Get Handler
     *
     * @return  string
     */
    public function getHandler() {
      return $this->handler;
    }
  
    /**
     * Open this DBA file
     *
     * @param   string mode default DBO_CREATE
     * @return  bool
     * @throws  io.IOException in case opening the file fails
     */
    public function open($mode= DBO_CREATE) {
      if (!is_resource($this->_fd= dba_open(
        $this->filename, 
        $mode, 
        $this->handler
      ))) {
        $this->_fd= -1;
        throw(new io::IOException(
          'Could not open '.$this->handler.'://'.$this->filename.' mode "'.$mode.'"'
        ));
      }
      return TRUE;
    }
    
    /**
     * Returns an iterator over the keys of this DBA file
     *
     * @return  io.dba.DBAIterator
     * @see     xp://io.dba.DBAIterator
     */
    public function iterator() {
      return new DBAIterator($this->_fd);
    }
    
    /**
     * Returns an array of keys
     *
     * Note: Do not use this for databases containing large amounts 
     * of keys, use the iterator() method instead.
     *
     * @return  string[] keys
     * @throws  io.IOException in case fetching the keys fails
     * @see     xp://io.dba.DBAFile#iterator
     */
    public function keys() {
      $keys= array();
      if (NULL === ($k= dba_firstkey($this->_fd))) {
        throw(new io::IOException('Could not fetch first key'));
      }
      while (is_string($k)) {
        $keys[]= $k;
        $k= dba_nextkey($this->_fd);
      }
      return $keys;
    }
    
    /**
     * Inserts the entry described with key and value into the 
     * database. Fails if an entry with the same key already 
     * exists. 
     *
     * @param   string key
     * @param   string value
     * @return  bool TRUE if the key was inserted, FALSE otherwise
     * @throws  io.IOException in case writing failed
     * @see     xp://io.dba.DBAFile#store
     */
    public function insert($key, $value) {
      if (!dba_insert($key, $value, $this->_fd)) {
      
        // dba_insert() failed due to the fact key already existed
        if (dba_exists($key, $this->_fd)) return FALSE;
        
        // dba_insert() failed to any other reason
        throw(new io::IOException('Could not insert key "'.$key.'"'));
      }
      return TRUE;
    }

    /**
     * Replaces or inserts the entry described with key and value 
     * into the database.
     *
     * @param   string key
     * @param   string value
     * @return  bool success
     * @throws  io.IOException in case writing failed
     * @see     xp://io.dba.DBAFile#insert
     */
    public function store($key, $value) {
      if (!dba_replace($key, $value, $this->_fd)) {
        throw(new io::IOException('Could not replace key "'.$key.'"'));
      }
      return TRUE;
    }
    
    /**
     * Removes a specified key from this database
     *
     * @param   string key
     * @return  bool success
     * @throws  io.IOException in case writing failed
     */
    public function delete($key) {
      if (!dba_delete($key, $this->_fd)) {
        throw(new io::IOException('Could not delete key "'.$key.'"'));
      }
      return TRUE;
    }
    
    /**
     * Checks for existance of a key
     *
     * @param   string key
     * @return  bool TRUE if the specified key exists
     */
    public function lookup($key) {
      return dba_exists($key, $this->_fd);
    }
    
    /**
     * Fetches the value associated with a specified key from this 
     * database. Returns FALSE in case the key cannot be found.
     *
     * @param   string key
     * @return  bool success
     * @throws  io.IOException in case reading failed
     */
    public function fetch($key) {
      $r= dba_fetch($key, $this->_fd);
      if (NULL === $r) {
        throw(new io::IOException('Could not fetch key "'.$key.'"'));
      }
      return $r;
    }
    
    /**
     * Synchronizes the database specified by handle. This will 
     * probably trigger a physical write to disk, if supported.
     *
     * @param   bool optimize default FALSE whether to optimize
     * @return  bool success
     * @throws  io.IOException in case saving and/or optimizing failed
     */    
    public function save($optimize= FALSE) {
      if ($optimize) if (!dba_optimize($this->_fd)) {
        throw(new io::IOException('Could not optimize database'));
      }
      if (!dba_sync($this->_fd)) {
        throw(new io::IOException('Could not save database'));
      }
      return TRUE;
    }
  
    /**
     * Close this database
     *
     * @return  bool
     */
    public function close() {
      $r= dba_close($this->_fd);
      $this->_fd= NULL;
      return $r;
    }
  }
?>
