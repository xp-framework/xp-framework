<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses('peer.Socket', 'util.registry.storage.RegistryStorage');

  /**
   * Remote Storage client implementation of key/vales pairs
   * The server component has the following syntax:
   * 
   * <pre>
   * GET <key>                  Gets a key
   * SET <key>=<value>          Inserts/Updates a key
   * DELE <key>                 Deletes a key
   * </pre>
   *
   * Not implemented:
   * <pre>
   * KEYS                       Returns all keys
   * SAVE                       Saves to disk
   * </pre>
   *
   * @purpose  A storage provider that uses a key server
   */
  class KeyServerStorage extends RegistryStorage {
    var
      $_sock= NULL;

    /**
     * Initialize this storage
     *
     * @access  public
     * @param   string host Hostname or IP
     * @param   int port default 6100 Port
     * @throws  IOException
     */
    function initialize($host, $port= 6100) {
      $this->_sock= &new Socket($host, $port);
      return $this->_sock->connect();
    }      
      
    /**
     * Private Helper-Fucktion
     * Sends a command and retreives the answer
     *
     * @access  private
     * @param   mixed* vars Arguments to sprintf
     * @return  string Stored Value
     * @throws  FormatException in case of an error
     */
    function _cmd() {
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
    function contains($key) {
      return FALSE !== $this->_cmd('GET %s', urlencode($this->id.'/'.$key));
    }

    /**
     * Get a key by it's name
     *
     * @access  public
     * @param   string key
     * @return  &mixed
     */
    function &get($key) {
      if (FALSE === ($return= $this->_cmd('GET %s', urlencode($this->id.'/'.$key)))) {
        return throw(new ElementNotFoundException($key.' does not exist'));
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
    function put($key, &$value, $permissions= 0666) {
      if (FALSE === $this->_cmd(
        'SET %s=%s', 
        urlencode($this->id.'/'.$key), 
        urlencode(serialize($value)))
      ) {
        return throw(new IOException($key.' could not be written'));
      }
      return TRUE;
    }

    /**
     * Remove a key
     *
     * @access  public
     * @param   string key
     */
    function remove($key) {
      if (FALSE === $this->_cmd('DELE %s', urlencode($this->id.'/'.$key))) {
        return throw(new IOException($key.' could not be deleted'));
      }
      return TRUE;
    }
  }  
?>
