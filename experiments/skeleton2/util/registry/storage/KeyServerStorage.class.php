<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'peer.Socket',
    'util.registry.storage.RegistryStorage'
  );

  /**
   * Remote Storage client implementation of key/vales pairs
   * The server component has the following syntax:
   * 
   * <pre>
   * GET <key>                  Gets a key
   * SET <key>=<value>          Inserts/Updates a key
   * DELE <key>                 Deletes a key
   * KEYS                       Returns all keys
   * </pre>
   *
   * Not implemented:
   * <pre>
   * SAVE                       Saves to disk
   * </pre>
   *
   * @purpose  A storage provider that uses a key server
   */
  class KeyServerStorage extends RegistryStorage {
    protected
      $_sock= NULL;

    /**
     * Initialize this storage
     *
     * @access  public
     * @param   string host Hostname or IP
     * @param   int port default 6100 Port
     * @throws  IOException
     */
    public function initialize($host, $port= 6100) {
      $this->_sock= new Socket($host, $port);
      return $this->_sock->connect();
    }      
      
    /**
     * Private Helper-Fucktion
     * Sends a command and retrieves the answer
     *
     * @access  private
     * @param   mixed* vars Arguments to sprintf
     * @return  string Stored Value
     * @throws  FormatException in case of an error
     */
    private function _cmd() {
      $args= func_get_args();
      $this->_sock->write($cmd= vsprintf($args[0]."\n", array_slice($args, 1)));
      $return= chop($this->_sock->read(65536));
      
      // +OK text saved.
      // -ERR SET format: key=val
      // -ERR not understood
      if ('+OK' != substr($return, 0, $i= strpos($return, ' '))) {
        return FALSE;
      }
      
      return substr($return, $i+ 1);
    }

    /**
     * Returns whether this storage contains the given key
     *
     * @access  public
     * @param   string key
     * @return  bool TRUE when this key exists
     */
    public function contains($key) {
      return FALSE !== self::_cmd('GET %s/%s', urlencode($this->id), urlencode($key));
    }

    /**
     * Get all keys
     *
     * @access  public
     * @return  string[] key
     */
    public function keys() { 
      if (FALSE === ($ret= self::_cmd('KEYS'))) return FALSE;
      return explode('|', $ret);
    }

    /**
     * Get a key by it's name
     *
     * @access  public
     * @param   string key
     * @return  &mixed
     */
    public function get($key) {
      if (FALSE === ($return= self::_cmd('GET %s/%s', urlencode($this->id), urlencode($key)))) {
        throw (new ElementNotFoundException($key.' does not exist'));
      }
      return unserialize(urldecode($return));
    }

    /**
     * Insert/update a key
     *
     * @access  public 
     * @param   string key
     * @param   &mixed value
     * @param   int permissions default 0666
     */
    public function put($key, $value, $permissions= 0666) {
      if (FALSE === self::_cmd(
        'SET %s/%s=%s', 
        urlencode($this->id), 
        urlencode($key),
        urlencode(serialize($value)))
      ) {
        throw (new IOException($key.' could not be written'));
      }
      return TRUE;
    }

    /**
     * Remove a key
     *
     * @access  public
     * @param   string key
     */
    public function remove($key) {
      if (FALSE === self::_cmd('DELE s/%s', urlencode($this->id), urlencode($key))) {
        throw (new IOException($key.' could not be deleted'));
      }
      return TRUE;
    }
    
    /**
     * Remove all keys
     *
     * @access  public
     */
    public function free() { 
      foreach (self::keys() as $key) {
        self::remove($key);
      }
    }
  }
?>
