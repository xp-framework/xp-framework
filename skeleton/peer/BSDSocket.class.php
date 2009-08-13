<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('peer.Socket');
  
  /**
   * BSDSocket implementation
   *
   * @test     xp://net.xp_framework.unittest.peer.sockets.BSDSocketTest
   * @see      php://sockets
   * @see      http://www.developerweb.net/sock-faq/ The UNIX Socket FAQ
   * @ext      sockets
   * @purpose  Provide an interface to the BSD sockets
   */
  class BSDSocket extends Socket {
    public
      $_eof     = FALSE,
      $domain   = AF_INET,
      $type     = SOCK_STREAM,
      $protocol = SOL_TCP,
      $options  = array();
    
    protected 
      $rq       = '';
    
    static function __static() {
      defined('TCP_NODELAY') || define('TCP_NODELAY', 1);
    }

    /**
     * Set Domain
     *
     * @param   int domain one of AF_INET or AF_UNIX
     * @throws  lang.IllegalStateException if socket is already connected
     */
    public function setDomain($domain) {
      if ($this->isConnected()) {
        throw new IllegalStateException('Cannot set domain on connected socket');
      }
      $this->domain= $domain;
    }

    /**
     * Get Domain
     *
     * @return  int
     */
    public function getDomain() {
      return $this->domain;
    }

    /**
     * Set Type
     *
     * @param   int type one of SOCK_STREAM, SOCK_DGRAM, SOCK_RAW, SOCK_SEQPACKET or SOCK_RDM
     * @throws  lang.IllegalStateException if socket is already connected
     */
    public function setType($type) {
      if ($this->isConnected()) {
        throw new IllegalStateException('Cannot set type on connected socket');
      }
      $this->type= $type;
    }

    /**
     * Get Type
     *
     * @return  int
     */
    public function getType() {
      return $this->type;
    }

    /**
     * Set Protocol
     *
     * @see     php://getprotobyname
     * @param   int protocol one of SOL_TCP or SOL_UDP
     * @throws  lang.IllegalStateException if socket is already connected
     */
    public function setProtocol($protocol) {
      if ($this->isConnected()) {
        throw new IllegalStateException('Cannot set protocol on connected socket');
      }
      $this->protocol= $protocol;
    }

    /**
     * Get Protocol
     *
     * @return  int
     */
    public function getProtocol() {
      return $this->protocol;
    }

    /**
     * Get last error
     *
     * @return  string error
     */  
    public function getLastError() {
      return sprintf('%d: %s', $e= socket_last_error($this->_sock), socket_strerror($e));
    }
    
    /**
     * Set socket option
     *
     * @param   int level
     * @param   int name
     * @param   mixed value
     * @see     php://socket_set_option
     */
    public function setOption($level, $name, $value) {
      $this->options[$level][$name]= $value;

      if ($this->isConnected()) {
        socket_set_option($this->_sock, $level, $name, $value);
      }
    }
    
    /**
     * Connect
     *
     * @param   float timeout default 2.0
     * @return  bool success
     * @throws  peer.ConnectException
     */
    public function connect($timeout= 2.0) {
      static $domains= array(
        AF_INET   => 'AF_INET',
        AF_UNIX   => 'AF_UNIX'
      );
      static $types= array(
        SOCK_STREAM     => 'SOCK_STREAM',
        SOCK_DGRAM      => 'SOCK_DGRAM',
        SOCK_RAW        => 'SOCK_RAW',
        SOCK_SEQPACKET  => 'SOCK_SEQPACKET',
        SOCK_RDM        => 'SOCK_RDM'
      );
      
      if ($this->isConnected()) return TRUE;    // Short-cuircuit this
      
      // Create socket...
      if (!($this->_sock= socket_create($this->domain, $this->type, $this->protocol))) {
        throw new ConnectException(sprintf(
          'Create of %s socket (type %s, protocol %s) failed: %d: %s',
          $domains[$this->domain],
          $types[$this->type],
          getprotobynumber($this->protocol),
          $e= socket_last_error(), 
          socket_strerror($e)
        ));
      }
      
      // Set options
      foreach ($this->options as $level => $pairs) {
        foreach ($pairs as $name => $value) {
          socket_set_option($this->_sock, $level, $name, $value);
        }
      }
      
      // ...and connect it
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
      if (FALSE === $r) throw new ConnectException(sprintf(
        'Connect to %s:%d failed: %s',
        $this->host,
        $this->port,
        $this->getLastError()
      ));

      $this->setTimeout($this->_timeout);
      return TRUE;
    }
    
    /**
     * Close socket
     *
     * @return  bool success
     */
    public function close() {
      if (!is_resource($this->_sock)) return FALSE;

      socket_close($this->_sock);
      $this->_sock= NULL;
      return TRUE;
    }
    /**
     * Set timeout
     *
     * @param   mixed _timeout
     */
    public function setTimeout($timeout) {
      $this->_timeout= $timeout;
      
      // Apply changes to already opened connection
      if (is_resource($this->_sock)) {
        $sec= floor($this->_timeout);
        $usec= ($this->_timeout- $sec) * 1000;
        socket_set_option($this->_sock, SOL_SOCKET, SO_RCVTIMEO, array('sec' => $sec, 'usec' => $usec));
        socket_set_option($this->_sock, SOL_SOCKET, SO_SNDTIMEO, array('sec' => $sec, 'usec' => $usec));
      }
    }

    /**
     * Set socket blocking
     *
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
        throw new SocketException(sprintf(
          'setBlocking (%s) failed: %s',
          ($blocking ? 'blocking' : 'nonblocking'),
          $this->getLastError()
        ));
      }
      
      return TRUE;      
    }

    /**
     * Selection helper
     *
     * @param   var r
     * @param   var w
     * @param   var w
     * @param   float timeout
     * @return  int
     * @see     php://socket_select
     */
    protected function _select($r, $w, $e, $timeout) {
      $tv_sec= (int)floor($timeout);
      $tv_usec= (int)(($timeout- $tv_sec) * 1000000);
      if (FALSE === ($n= socket_select($r, $w, $e, $tv_sec, $tv_usec))) {
        throw new SocketException('Select failed: '.$this->getLastError());
      }
      return $n;
    }
        
    /**
     * Returns whether there is data that can be read
     *
     * @param   float timeout default NULL Timeout value in seconds (e.g. 0.5)
     * @return  bool there is data that can be read
     * @throws  peer.SocketException in case of failure
     */
    public function canRead($timeout= NULL) {
      return $this->_select(array($this->_sock), NULL, NULL, $timeout) > 0;
    }
    
    /**
     * Returns whether eof has been reached
     *
     * @return  bool
     */
    public function eof() {
      return $this->_eof;
    }
    
    /**
     * Reading helper
     *
     * @param   int maxLen
     * @param   int type PHP_BINARY_READ or PHP_NORMAL_READ
     * @param   bool chop
     * @return  string data
     */
    protected function _read($maxLen, $type, $chop= FALSE) {
      $res= '';
      if (!$this->_eof && 0 === strlen($this->rq)) {
        if (!$this->_select(array($this->_sock), NULL, NULL, $this->_timeout)) {
          throw new SocketTimeoutException('Read of '.$maxLen.' bytes failed', $this->_timeout);
        }
        $res= @socket_read($this->_sock, $maxLen);
        if (FALSE === $res || NULL === $res) {
          $error= socket_last_error($this->_sock);
          if (0 === $error || SOCKET_ECONNRESET === $error) {
            $this->_eof= TRUE;
            return NULL;
          }
          throw new SocketException('Read of '.$maxLen.' bytes failed: '.$this->getLastError());
        } else if ('' === $res) {
          $this->_eof= TRUE;
        }
      }
      
      $read= $this->rq.$res;
      if (PHP_NORMAL_READ === $type) {
        if ('' === $read) return NULL;
        $c= strcspn($read, "\n");
        $this->rq= substr($read, $c+ 1);
        $chunk= substr($read, 0, $c+ 1);
        return $chop ? chop($chunk) : $chunk;
      } else if (PHP_BINARY_READ === $type) {
        if ('' === $read) return '';
        $this->rq= substr($read, $maxLen);
        return substr($read, 0, $maxLen);
      }
    }
        
    /**
     * Read data from a socket
     *
     * @param   int maxLen maximum bytes to read
     * @return  string data
     * @throws  peer.SocketException
     */
    public function read($maxLen= 4096) {
      return $this->_read($maxLen, PHP_NORMAL_READ);
    }

    /**
     * Read data from a socket
     *
     * @param   int maxLen maximum bytes to read
     * @return  string data
     * @throws  peer.SocketException
     */
    public function readLine($maxLen= 4096) {
      return $this->_read($maxLen, PHP_NORMAL_READ, TRUE);
    }
    
    /**
     * Read data from a socket (binary-safe)
     *
     * @param   int maxLen maximum bytes to read
     * @return  string data
     * @throws  peer.SocketException
     */
    public function readBinary($maxLen= 4096) {
      return $this->_read($maxLen, PHP_BINARY_READ);
    }

    /**
     * Write a string to the socket
     *
     * @param   string str
     * @return  int bytes written
     * @throws  peer.SocketException
     */
    public function write($str) {
      $bytesWritten= socket_write($this->_sock, $str, strlen($str));
      if (FALSE === $bytesWritten || NULL === $bytesWritten) {
        throw new SocketException('Write of '.$len.' bytes to socket failed: '.$this->getLastError());
      }
      
      return $bytesWritten;
    }
  }
?>
