<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses('io.File', 'io.FileUtil');

  /**
   * The storage daemon is a TCP/IP server that allows you to store
   * and retrieve data.
   *
   * Socket syntax (>>> is request, <<< is response, ### a comment):
   * <pre>
   *   ### Add an entry
   *   >>> ADD user a:2:{i:0;i:9;i:1;i:2;s:9:"thekid.de";}
   *   <<< +OK b:1;
   *
   *   ### Clear storage
   *   >>> CLEAR user
   *   <<< +OK b:1;
   *
   *   ### Get stored data
   *   >>> GET user
   *   <<< +OK a:1:{i:0;a:2:{i:0;i:9;i:1;i:2;s:9:"thekid.de";}}
   * </pre>
   *
   * Error messages are returned as following (example):
   * <pre>
   *   >>> GET nonexistant
   *   <<< -ERR Cannot get filesize for history.friebe"
   * </pre>
   *
   * All data is serialized using PHP's serialize() functionality
   *
   * @see      xp://peer.server.ConnectionListener
   * @purpose  Connection Listener for the storage daemon
   */
  class StorageListener extends ConnectionListener {
  
    /**
     * Method to be triggered when a client connects
     *
     * @access  public
     * @param   &peer.server.ConnectionEvent event
     */
    function connected(&$event) {
    }
    
    /**
     * Handle method "GET"
     *
     * @access  protected
     * @param   string name
     * @return  string data
     */
    function handleGet($name) {
      clearstatcache();
      return FileUtil::getContents(new File($name));
    }

    /**
     * Handle method "ADD"
     *
     * @access  protected
     * @param   string name
     * @param   string serial serial representation of data
     * @return  string b:1; on success
     * @throws  lang.FormatException in case serial data is corrupt
     */
    function handleAdd($name, $serial) {
      $data= unserialize($serial);
      if (xp::errorAt(__FILE__, __LINE__ - 1)) {
        throw(new FormatException('Data "'.$serial.'" format not recognized'));
        return FALSE;
      }
      
      // Create storage file if it does not exist
      $f= &new File($name);
      if (!$f->exists()) {
        $f->open(FILE_MODE_WRITE);
        $a= array();
      } else {
        clearstatcache();
        $f->open(FILE_MODE_READWRITE);
        $a= unserialize($f->read($f->size()));
        if (xp::errorAt(__FILE__, __LINE__ - 1)) {
          $f->close();
          delete($f);
          throw(new FormatException('Storage corrupt'));
          return FALSE;
        }
      }
      
      // Prepend data to the beginning of the history
      array_unshift($a, $data);
      
      // Write it back to the file
      $f->rewind();
      $f->write(serialize($a));
      $f->close();
      
      // Clean up and return
      delete($f);
      return serialize(TRUE);
    }
    
    /**
     * Handle method "CLEAR"
     *
     * @access  protected
     * @param   string name
     * @return  string b:1; on success
     */
    function handleClear($name) {
      $f= &new File($name);
      $f->unlink();

      // Clean up and return
      delete($f);
      return serialize(TRUE);
    }
    
    /**
     * Method to be triggered when a client has sent data
     *
     * @access  public
     * @param   &peer.server.ConnectionEvent event
     */
    function data(&$event) {
      
      // Scan input string
      $cmd= sscanf($event->data, "%s %s %[^\r]");
      try(); {
        $return= call_user_func_array(
          array(&$this, 'handle'.$cmd[0]), 
          array_slice($cmd, 1)
        );
        if ($return) $response= '+OK '.$return;
      } if (catch('Exception', $e)) {
        $e->printStackTrace();
        $response= '-ERR '.$e->getMessage();
        delete($e);
      }
      
      $event->stream->write($response."\r\n");
      unset($response, $return);
    }
    
    /**
     * Method to be triggered when a client disconnects
     *
     * @access  public
     * @param   &peer.server.ConnectionEvent event
     */
    function disconnected(&$event) {
    }
    
    /**
     * Method to be triggered when a communication error occurs
     *
     * @access  public
     * @param   &peer.server.ConnectionEvent event
     */
    function error(&$event) {
    }  
  }
?>
