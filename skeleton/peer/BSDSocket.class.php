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
    var
      $_eof     = FALSE,
      $domain   = AF_INET,
      $type     = SOCK_STREAM,
      $protocol = SOL_TCP;

    /**
     * Set Domain
     *
     * @access  public
     * @param   int domain one of AF_INET or AF_UNIX
     * @throws  lang.IllegalStateException if socket is already connected
     */
    function setDomain($domain) {
      if ($this->isConnected()) {
        return throw(new IllegalStateException('Cannot set domain on connected socket'));
      }
      $this->domain= $domain;
    }

    /**
     * Get Domain
     *
     * @access  public
     * @return  int
     */
    function getDomain() {
      return $this->domain;
    }

    /**
     * Set Type
     *
     * @access  public
     * @param   int type one of SOCK_STREAM, SOCK_DGRAM, SOCK_RAW, SOCK_SEQPACKET or SOCK_RDM
     * @throws  lang.IllegalStateException if socket is already connected
     */
    function setType($type) {
      if ($this->isConnected()) {
        return throw(new IllegalStateException('Cannot set type on connected socket'));
      }
      $this->type= $type;
    }

    /**
     * Get Type
     *
     * @access  public
     * @return  int
     */
    function getType() {
      return $this->type;
    }

    /**
     * Set Protocol
     *
     * @access  public
     * @see     php://getprotobyname
     * @param   int protocol one of SOL_TCP or SOL_UDP
     * @throws  lang.IllegalStateException if socket is already connected
     */
    function setProtocol($protocol) {
      if ($this->isConnected()) {
        return throw(new IllegalStateException('Cannot set protocol on connected socket'));
      }
      $this->protocol= $protocol;
    }

    /**
     * Get Protocol
     *
     * @access  public
     * @return  int
     */
    function getProtocol() {
      return $this->protocol;
    }

    /**
     * Get last error
     *
     * @access  public
     * @return  string error
     */  
    function getLastError() {
      return sprintf('%d: %s', $e= socket_last_error($this->_sock), socket_strerror($e));
    }
    
    /**
     * Connect
     *
     * @access  public
     * @return  bool success
     * @throws  peer.ConnectException
     */
    function connect() {
      if ($this->isConnected()) return TRUE;
      
      // Create and connect the socket
      $this->_sock= socket_create($this->domain, $this->type, $this->protocol);
      switch ($this->domain) {
        case AF_INET: {
          $r= socket_connect($this->_sock, gethostbyname($this->host), $this->port);
          break;
        }
        
        case AF_UNIX: {
          $r= socket_connect($this->_sock, $this->host);
          break;
        }
      }
      
      // Check return status
      if (FALSE === $r) return throw(new ConnectException(sprintf(
        'Connect to %s:%d failed: %s',
        $this->host,
        $this->port,
        $this->getLastError()
      )));

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
     * Set socket blocking
     *
     * @access  public
     * @param   bool blocking
     * @return  bool success
     * @throws  peer.SocketException
     */
    function setBlocking($blocking) {
      if ($blocking) {
        $ret= socket_set_block($this->_sock);
      } else {
        $ret= socket_set_nonblock($this->_sock);
      }
      if (FALSE === $ret) {
        return throw(new SocketException(sprintf(
          'setBlocking (%s) failed: %s',
          ($blocking ? 'blocking' : 'nonblocking'),
          $this->getLastError()
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
    function canRead($timeout= NULL) {
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
        return throw(new SocketException('Select failed: '.$this->getLastError()));
      }
      
      return $n > 0;
    }
    
    /**
     * Returns whether eof has been reached
     *
     * @access  public
     * @return  bool
     */
    function eof() {
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
    function _read($maxLen, $type, $chop= FALSE) {
      if (FALSE === ($res= socket_read($this->_sock, $maxLen, $type))) {
        return throw(new SocketException('Read failed: '.$this->getLastError()));
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
    function read($maxLen= 4096) {
      return $this->_read($maxLen, PHP_NORMAL_READ);
    }

    /**
     * Read data from a socket
     *
     * @access  public
     * @param   int maxLen maximum bytes to read
     * @return  string data
     * @throws  peer.SocketException
     */
    function readLine($maxLen= 4096) {
      return $this->_read($maxLen, PHP_NORMAL_READ, TRUE);
    }
    
    /**
     * Read data from a socket (binary-safe)
     *
     * @access  public
     * @param   int maxLen maximum bytes to read
     * @return  string data
     * @throws  peer.SocketException
     */
    function readBinary($maxLen= 4096) {
      return $this->_read($maxLen, PHP_BINARY_READ);
    }

    /**
     * Write a string to the socket
     *
     * @access  public
     * @param   string str
     * @return  int bytes written
     * @throws  peer.SocketException
     */
    function write($str) {
      $bytesWritten= socket_write($this->_sock, $str, strlen($str));
      if (FALSE === $bytesWritten) {
        return throw(new SocketException('Write failed: '.$this->getLastError()));
      }
      return $bytesWritten;
    }

  }
?>
