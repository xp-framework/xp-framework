<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('peer.Socket');

  /**
   * BSDSocket implementation
   *
   * @purpose  Provide an interface to the BSD sockets                    
   * @see      php://sockets                                              
   * @see      http://www.developerweb.net/sock-faq/ The UNIX Socket FAQ  
   * @ext      sockets                                                    
   */
  class BSDSocket extends Socket {
    public
      $_eof = FALSE;
      
    /**
     * Get last error
     *
     * @access  public
     * @return  string error
     */  
    public function getLastError() {
      return sprintf('%d: %s', $e= socket_last_error($this->_sock), socket_strerror($e));
    }
    
    /**
     * Connect
     *
     * @access  public
     * @return  bool success
     * @throws  peer.ConnectException
     */
    public function connect() {
      if (self::isConnected()) return TRUE;
      
      $this->_sock= socket_create(AF_INET, SOCK_STREAM, 0);
      if (FALSE === socket_connect(
        $this->_sock, 
        gethostbyname($this->host),
        $this->port
      )) {
        throw (new ConnectException(sprintf(
          'Connect to %s:%d failed: %s',
          $this->host,
          $this->port,
          self::getLastError()
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
    public function close() {
      $res= socket_close($this->_sock);
      $this->_sock= NULL;
      return $res;
    }

    /**
     * Set socket blocking
     *
     * @access  public
     * @param   bool blocking
     * @return  bool success
     * @throws  peer.SocketException
     */
    public function setBlocking($blocking) {
      if ($blocking) {
        $ret= socket_set_block($this->_sock);
      } else {
        $ret= socket_set_nonblock($this->_sock);
      }
      if (FALSE === $ret) {
        throw (new SocketException(sprintf(
          'setBlocking (%s) failed: %s',
          ($blocking ? 'blocking' : 'nonblocking'),
          self::getLastError()
        )));
      }
      
      return TRUE;      
    }
        
    /**
     * Returns whether there is data that can be read
     *
     * @access  public
     * @param   float timeout default NULL Timeout value in seconds (e.g. 0.5)
     * @return  bool there is data that can be read
     * @throws  peer.SocketException in case of failure
     */
    public function canRead($timeout= NULL) {
      if (NULL === $timeout) {
        $tv_sec= $tv_usec= 0;
      } else {
        $tv_sec= intval(floor($timeout));
        $tv_usec= intval(($timeout - floor($timeout)) * 1000000);
      }
      
      if (FALSE === ($n= socket_select(
        $r= array(&$this->_sock),             // Read
        $w= NULL,                             // Write
        $e= NULL,                             // Except
        $tv_sec,
        $tv_usec
      ))) {
        throw (new SocketException('Select failed: '.self::getLastError()));
      }
      
      return $n > 0;
    }
    
    /**
     * Returns whether eof has been reached
     *
     * @access  public
     * @return  bool
     */
    public function eof() {
      return $this->_eof;
    }

    /**
     * Private helper function
     *
     * @access  private
     * @param   int maxLen
     * @param   int type PHP_BINARY_READ or PHP_NORMAL_READ
     * @return  string data
     */
    private function _read($maxLen, $type, $chop= FALSE) {
      if (FALSE === ($res= socket_read($this->_sock, $maxLen, $type))) {
        throw (new SocketException('Read failed: '.self::getLastError()));
      }
      if ('' === $res) {
        $this->_eof= TRUE;
        return NULL;
      } else {
        return $chop ? chop($res) : $res;
      }
    }
        
    /**
     * Read data from a socket
     *
     * @access  public
     * @param   int maxLen maximum bytes to read
     * @return  string data
     * @throws  peer.SocketException
     */
    public function read($maxLen= 4096) {
      return self::_read($maxLen, PHP_NORMAL_READ);
    }

    /**
     * Read data from a socket
     *
     * @access  public
     * @param   int maxLen maximum bytes to read
     * @return  string data
     * @throws  peer.SocketException
     */
    public function readLine($maxLen= 4096) {
      return self::_read($maxLen, PHP_NORMAL_READ, TRUE);
    }
    
    /**
     * Read data from a socket (binary-safe)
     *
     * @access  public
     * @param   int maxLen maximum bytes to read
     * @return  string data
     * @throws  peer.SocketException
     */
    public function readBinary($maxLen= 4096) {
      return self::_read($maxLen, PHP_BINARY_READ);
    }

    /**
     * Write a string to the socket
     *
     * @access  public
     * @param   string str
     * @return  int bytes written
     * @throws  peer.SocketException
     */
    public function write($str) {
      $bytesWritten= socket_write($this->_sock, $str, strlen($str));
      if (FALSE === $bytesWritten) {
        throw (new SocketException('Write failed: '.self::getLastError()));
      }
      return $bytesWritten;
    }

  }
?>
