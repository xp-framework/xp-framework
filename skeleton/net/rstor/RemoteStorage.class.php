<?php
/* Diese Klasse ist Teil des XP-Frameworks
 *
 * $Id$
 */
  uses('net.Socket');

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
   * @access  public
   */
  class RemoteStorage extends Socket {
    var
      $_timeout= 1;
      
    /**
     * Private Helper-Fucktion
     * Sends a command and retreives the answer
     *
     * @access  private
     * @param   mixed* vars Arguments to sprintf
     * @return  string Stored Value
     * @throws  Exception in case of an error
     */
    function _cmd() {
      $args= func_get_args();
      $this->write(vsprintf($args[0]."\n", array_slice($args, 1)));
      $return= chop($this->read(65536));
      
      // +OK text saved.
      // -ERR SET format: key=val
      // -ERR not understood
      if ('+OK' != substr($return, 0, $i= strpos($return, ' '))) return throw(new Exception(
        $return
      ));
      return substr($return, $i+ 1);
    }
    
    /**
     * Deletes a key
     *
     * @access  public
     * @param   string key Keyname
     * @return  bool Success
     * @throws  Exception in case of an error
     */
    function deleteKey($key) {
      return FALSE !== $this->_cmd('DELE %s', $key);
    }
    
    /**
     * Read a key
     *
     * @access  public
     * @param   string key Key
     * @return  mixed Value
     * @throws  Exception in case of an error or if the key doesn't exist
     */
    function readKey($key) {
      if (FALSE === ($return= $this->_cmd('GET %s', $key))) return FALSE;
      return unserialize($return);
    }
    
    /**
     * Write a key. Update if it exists
     *
     * @access  public
     * @param   string key Key
     * @param   mixed value Value
     * @return  bool Success
     * @throws  Exception in case of an error
     */
    function writeKey($key, $value) {
      return FALSE !== $this->_cmd('SET %s=%s', $key, serialize($value));
    }
    
    /**
     * Check for existance of key
     *
     * @access  public
     * @param   string key Name of Key
     * @return  bool Key exists
     */
    function hasKey($key) {
      try(); {
        $return= (FALSE !== $this->_cmd('GET %s', $key));
      } if (catch('Exception', $e)) {
        return FALSE;
      }
      return $return;
    }
    
    /**
     * Get all existing keys
     *
     * @access  public
     * @return  array Keys
     * @throws  Exception in case of an error
     */
    function getKeys() {
      if (FALSE === ($return= $this->_cmd('KEYS'))) return FALSE;
      return explode('|', substr($return, 0, -1));
    }
  }  
?>
