<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * (Insert class' description here)
   *
   * @purpose  purpose
   */
  class FileSystemPersistence extends Object {
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
    #[@command(name= 'session_create', args= array('%d'))]
    public function create($timeout) {
      static $i;
      $i++;

      $id= 'ac111d0f'.$i.$timeout;
      $this->s[$id]= array();
      return $id;
    }

    /**
     * Check whether a given session is valid
     *
     * @param   string session id
     */
    #[@command(name= 'session_isvalid', args= array('%s'))]
    public function valid($id) {
      return isset($this->s[$id]);
    }

    /**
     * Terminate a session
     *
     * @param   string session id
     */
    #[@command(name= 'session_terminate', args= array('%s'))]
    public function terminate($id) {
      unset($this->s[$id]);
      return TRUE;
    }

    /**
     * Get all keys
     *
     * @param   string session id
     * @param   string area
     */
    #[@command(name= 'session_keys', args= array('%s %s'))]
    public function keys($id, $area) {
      if (!($keys= array_keys($this->s[$id][$area]))) return NULL;
      return urlencode(implode(' ', $keys));
    }

    /**
     * Read a value
     *
     * @param   string id session id
     * @param   string area
     * @param   string key
     */
    #[@command(name= 'var_read', args= array('%s %s %s'))]
    public function read($id, $area, $key) {
      return $this->s[$id][$area][$key];
    }

    /**
     * Write a value
     *
     * @param   string id session id
     * @param   string area
     * @param   string key
     * @param   string value
     */
    #[@command(name= 'var_write', args= array("%s %s %s %[^\n]"))]
    public function write($id, $area, $key, $value) {
      $this->s[$id][$area][$key]= $value;
      return TRUE;
    }

    /**
     * Delete a value
     *
     * @param   string id session id
     * @param   string area
     * @param   string key
     */
    #[@command(name= 'var_delete', args= array('%s %s %s'))]
    public function delete($id, $area, $key, $value) {
      unset($this->s[$id][$area][$key]);
      return TRUE;
    }

    /**
     * Lock a session
     *
     * @param   string id session id
     */
    #[@command(name= 'session_lock', args= array('%s'))]
    public function lock($id) {
      $this->l[$id]= TRUE;
      return TRUE;
    }

    /**
     * Unlock a session
     *
     * @param   string id session id
     */
    #[@command(name= 'session_unlock', args= array('%s'))]
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
    #[@command(name= 'session_associate', args= array('%s %s'))]
    public function associate($id, $uid) {
      return TRUE;
    }
  }
?>
