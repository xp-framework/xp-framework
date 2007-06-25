<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('persist.SessionPersistence', 'io.File', 'io.FileUtil');

  /**
   * (Insert class' description here)
   *
   * @purpose  SessionPersistence implementation
   */
  class FileSystemPersistence extends Object implements SessionPersistence {
    protected
      $dir= '';
      
    /**
     * Constructor
     *
     * @param   string dir
     */
    public function __construct($dir) {
      $this->dir= rtrim(realpath($dir), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
    }
    
    /**
     * Create a new session
     *
     * @param   int timeout
     * @return  string session id
     */
    public function create($identifier, $timeout) {
      static $id= 0;
      
      $id++;
      $this->s[$identifier.$id]= array();
      return $identifier.$id;
    }
    
    public function load($id) {
      $this->s[$id]= (array)unserialize(FileUtil::getContents(new File($this->dir.$id.'.sess')));
    }

    public function save($id) {
      FileUtil::setContents(new File($this->dir.$id.'.sess'), serialize($this->s[$id]));
    }

    /**
     * Check whether a given session is valid
     *
     * @param   string session id
     */
    public function reset($id) {
      $this->s[$id]= array();
    }

    /**
     * Check whether a given session is valid
     *
     * @param   string session id
     */
    public function valid($id) {
      return isset($this->s[$id]);
    }

    /**
     * Terminate a session
     *
     * @param   string session id
     */
    public function terminate($id) {
      unset($this->s[$id]);
      unlink($this->dir.$id.'.sess');
      return TRUE;
    }

    /**
     * Get all keys
     *
     * @param   string session id
     * @param   string area
     */
    public function keys($id) {
      return array_keys($this->s[$id]);
    }

    /**
     * Read a value
     *
     * @param   string id session id
     * @param   string area
     * @param   string key
     */
    public function read($id, $key) {
      return $this->s[$id][$key];
    }

    /**
     * Read a value
     *
     * @param   string id session id
     * @param   string area
     * @param   string key
     */
    public function exists($id, $key) {
      return isset($this->s[$id]);
    }

    /**
     * Write a value
     *
     * @param   string id session id
     * @param   string area
     * @param   string key
     * @param   string value
     */
    public function write($id, $key, $value) {
      $this->s[$id][$key]= $value;
      return TRUE;
    }

    /**
     * Delete a value
     *
     * @param   string id session id
     * @param   string area
     * @param   string key
     */
    public function delete($id, $key, $value) {
      unset($this->s[$id][$key]);
      return TRUE;
    }

    /**
     * Lock a session
     *
     * @param   string id session id
     */
    public function lock($id) {
      $this->l[$id]= TRUE;
      return TRUE;
    }

    /**
     * Unlock a session
     *
     * @param   string id session id
     */
    public function unlock($id) {
      unset($this->l[$id]);
      return TRUE;
    }

    /**
     * Associate a session to a user
     *
     * @param   string id session id
     * @param   string uid user id
     */
    public function associate($id, $uid) {
      return TRUE;
    }
  }
?>
