<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('io.IOException');

  /**
   * BSDSocket implementation
   *
   * @see	php://sockets
   */
  class BSDSocket extends Object {
    var 
      $host,
      $port;
      
    var
      $_sock;
    
    /**
     * Get last error
     *
     * @access  
     * @param   
     * @return  
     */  
    function getLastError() {
      return sprintf('%d: %s', $e= socket_last_error($this->_sock), socket_strerror($e));
    }
    
    /**
     * Returns whether a connection has been established
     *
     * @access  public
     * @return  bool connected
     */
    function isConnected() {
      return isset($this->_sock) && is_resource($this->_sock);
    }
    
    /**
     * Connect
     *
     * @access  public
     * @return  bool success
     * @throws  IOException
     */
    function connect() {
      if ($this->isConnected()) return TRUE;
      
      $this->_sock= socket_create(AF_INET, SOCK_STREAM, 0);
      if (FALSE === socket_connect(
        $this->_sock, 
        gethostbyname($this->host),
        $this->port
      )) {
        return throw(new IOException(sprintf(
          'Connect to %s:%d failed: %s',
          $this->host,
          $this->port,
          $this->getLastError()
        )));
      }
      return TRUE;
    }
    
    /**
     * Set socket blocking
     *
     * @access  public
     * @param   bool blocking
     * @return  bool success
     * @throws  IOException
     */
    function setBlocking($blocking) {
      if ($blocking) {
        $ret= socket_set_block($this->_sock);
      } else {
        $ret= socket_set_nonblock($this->_sock);
      }
      if (FALSE === $ret) {
        return throw(new IOException(sprintf(
          'setBlocking (%s) failed: %s',
          ($blocking ? 'blocking' : 'nonblocking'),
          $this->getLastError()
        )));
      }
      
      return TRUE;      
    }
    
    /**
     * Close socket
     *
     * @access  public
     * @return  bool success
     */
    function close() {
      $res= socket_close($this->_sock);
      $this->_sock= NULL;
      return $res;
    }
    
    /**
     * Returns whether there is data that can be read
     *
     * @access  public
     * @param   float timeout default NULL Timeout value in seconds (e.g. 0.5)
     * @return  bool there is data that can be read
     * @throws  IOException in case of failure
     */
    function canRead($timeout= NULL) {
      if (NULL === $timeout) {
        $tv_sec= $tv_usec= 0;
      } else {
        $tv_sec= intval(floor($timeout));
        $tv_usec= intval(($timeout - floor($timeout)) * 1000000);
      }
      
      if (FALSE === ($n= socket_select(
        $r= array(&$this->_sock), 			// Read
        $w= NULL, 							// Write
        $e= NULL, 							// Except
        $tv_sec,
        $tv_usec
      ))) {
        return throw(new IOException('Select failed: '.$this->getLastError()));
      }
      return $n > 0;
    }
    
    /**
     * Read data from a socket
     *
     * @access  public
     * @param   int maxLen maximum bytes to read
     * @return  string data
     * @throws	IOException
     */
    function read($maxLen= 4096) {
      $res= socket_read($this->_sock, 4096);
      # DEBUG echo "RECV(".$maxLen.") ".var_export($res, 1)."\n";
      
      if (FALSE === $res) {
        return throw(new IOException('Read failed: '.$this->getLastError()));
      }
      return $res;
    }
    
    /**
     * Write a string to the socket
     *
     * @access  public
     * @param   string str
     * @return  int bytes written
     * @throws	IOException
     */
    function write($str) {
      $bytesWritten= socket_write($this->_sock, $str, strlen($str));
      # DEBUG echo "SEND(".var_export($str, 1).") ".var_export($bytesWritten, 1)."\n";
      if (FALSE === $bytesWritten) {
        return throw(new IOException('Write failed: '.$this->getLastError()));
      }
      return $bytesWritten;
    }
    

  }
?>
