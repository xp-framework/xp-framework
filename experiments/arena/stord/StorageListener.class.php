<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses('io.File', 'io.FileUtil', 'lang.MethodNotImplementedException');
  
  define('APPEND',  0x0000);
  define('PREPEND', 0x0001);
  define('INSERT',  0x0002);
  define('UPDATE',  0x0003);
  define('DELETE',  0x0004);  
  define('SET',     0x0005);

  /**
   * The storage daemon is a TCP/IP server that allows you to store
   * and retrieve data.
   *
   * Socket syntax (>>> is request, <<< is response, ### a comment):
   * <pre>
   *   ### Prepend an entry to a list-based storage
   *   >>> PREPEND user a:2:{i:0;i:9;i:1;i:2;s:9:"thekid.de";}
   *   <<< +OK b:1;
   *
   *   ### Append an entry to a list-based storage
   *   >>> APPEND user a:2:{i:0;i:9;i:1;i:2;s:9:"thekid.de";}
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
   *   >>> ADD nonexistant N;
   *   <<< -ERR Cannot get filesize for nonexistant"
   * </pre>
   *
   * All data is serialized using PHP's serialize() functionality
   *
   * @see      xp://peer.server.ConnectionListener
   * @purpose  Connection Listener for the storage daemon
   */
  class StorageListener extends ConnectionListener {
    var
      $data= array();

    /**
     * Handle method "GET"
     *
     * @access  protected
     * @param   string name
     * @return  string data
     */
    function handleGet($name) {
      $f= &new File($name);
      if (!$f->exists()) return 'N;';
      
      clearstatcache();
      return FileUtil::getContents($f);
    }
    
    /**
     * Helper method
     *
     * @access  protected
     * @param   string name
     * @param   string serial serial representation of data
     * @param   int operation one of APPEND, PREPEND, INSERT, DELTE, UPDATE or SET
     * @param   int position default 0 where to insert for set
     * @return  string b:1; on success
     * @throws  lang.FormatException in case serial data is corrupt
     */
    function _write($name, $serial, $operation, $position= 0) {
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
        $f->rewind();
      }
      
      switch ($operation) {
        case PREPEND:
          array_unshift($a, $data);
          break;
        
        case APPEND:
          array_push($a, $data);
          break;
        
        case INSERT:
          $a= array_merge(
            array_slice($a, 0, $position), 
            $data,
            array_slice($a, $position)
          );
          break;
        
        case DELETE:
          unset($a[$position]);
          break;
        
        case UPDATE:
          $a[$position]= $data;
          break;
        
        case SET:
          $a= $data;
          break;
      }
      
      // Write it back to the file
      $f->write(serialize($a));
      $f->close();
      
      // Clean up and return
      delete($f);
      return serialize(TRUE);
    }

    /**
     * Handle method "PREPEND"
     *
     * @access  protected
     * @param   string name
     * @param   string serial serial representation of data
     * @return  string b:1; on success
     * @throws  lang.FormatException in case serial data is corrupt
     */
    function handlePrepend($name, $serial) {
      return $this->_write($name, $serial, PREPEND);
    }

    /**
     * Handle method "APPEND"
     *
     * @access  protected
     * @param   string name
     * @param   string serial serial representation of data
     * @return  string b:1; on success
     * @throws  lang.FormatException in case serial data is corrupt
     */
    function handleAppend($name, $serial) {
      return $this->_write($name, $serial, APPEND);
    }

    /**
     * Handle method "INSERT"
     *
     * @access  protected
     * @param   string name
     * @param   string data containing a number defining the position and the serial data
     * @return  string b:1; on success
     * @throws  lang.FormatException in case serial data is corrupt
     */
    function handleInsert($name, $data) {
      sscanf($data, "%d %[^\r]", $position, $serial);
      return $this->_write($name, $serial, INSERT, $position);
    }

    /**
     * Handle method "UPDATE"
     *
     * @access  protected
     * @param   string name
     * @param   string data containing a number defining the position and the serial data
     * @return  string b:1; on success
     * @throws  lang.FormatException in case serial data is corrupt
     */
    function handleUpdate($name, $data) {
      sscanf($data, "%d %[^\r]", $position, $serial);
      return $this->_write($name, $serial, UPDATE, $position);
    }

    /**
     * Handle method "DELETE"
     *
     * @access  protected
     * @param   string name
     * @param   string data a number defining the position
     * @return  string b:1; on success
     * @throws  lang.FormatException in case serial data is corrupt
     */
    function handleDelete($name, $data) {
      return $this->_write($name, NULL, DELETE, (int)$data);
    }

    /**
     * Handle method "SET"
     *
     * @access  protected
     * @param   string name
     * @param   string serial serial representation of data
     * @return  string b:1; on success
     * @throws  lang.FormatException in case serial data is corrupt
     */
    function handleSet($name, $serial) {
      return $this->_write($name, $serial, SET);
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
     * Method to be triggered when a client connects
     *
     * @access  public
     * @param   &peer.server.ConnectionEvent event
     */
    function connected(&$event) {
      $this->data[$event->stream->hashCode()]= '';
    }
    
    /**
     * Method to be triggered when a client has sent data
     *
     * @access  public
     * @param   &peer.server.ConnectionEvent event
     */
    function data(&$event) {
      $this->data[$event->stream->hashCode()].= $event->data;
      if ("\n" != $event->data{strlen($event->data)- 1}) {        // Wait for more data
        return;
      }
      
      // Scan input string
      $cmd= sscanf($this->data[$event->stream->hashCode()], "%s %s %[^\r]");
      $this->data[$event->stream->hashCode()]= '';
      try(); {
        if (!method_exists($this, 'handle'.$cmd[0])) {
          throw(new MethodNotImplementedException('Operation not supported', $cmd[0]));
        } else {
          $return= call_user_func_array(
            array(&$this, 'handle'.$cmd[0]), 
            array_slice($cmd, 1)
          );
          if ($return) $response= '+OK '.$return;
        }
      } if (catch('Exception', $e)) {
        $e->printStackTrace();
        $response= '-ERR '.$e->getMessage();
        delete($e);
      }
      
      $event->stream->write($response."\r\n");
      unset($response, $return);
    }
  }
?>
