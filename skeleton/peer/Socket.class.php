<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'peer.ConnectException',
    'peer.SocketTimeoutException',
    'peer.SocketException',
    'peer.SocketInputStream',
    'peer.SocketOutputStream'
  );
  
  /**
   * Socket class
   *
   * @test     xp://net.xp_framework.unittest.peer.sockets.SocketTest
   * @see      php://network
   * @purpose  Basic TCP/IP socket
   */
  class Socket extends Object {
    public
      $host     = '',
      $port     = 0;
      
    public
      $_sock    = NULL,
      $_prefix  = '',
      $_timeout = 60;
    
    /**
     * Constructor
     *
     * Note: When specifying a numerical IPv6 address (e.g. fe80::1)
     * as value for the parameter "host", you must enclose the IP in
     * square brackets.
     *
     * @param   string host hostname or IP address
     * @param   int port
     * @param   resource socket default NULL
     */
    public function __construct($host, $port, $socket= NULL) {
      $this->host= $host;
      $this->port= $port;
      $this->_sock= $socket;
    }
    
    /**
     * Get last error. A very inaccurate way of going about error messages since
     * any PHP error/warning is returned - but since there's no function like
     * flasterror() we must rely on this
     *
     * @return  string error
     */  
    public function getLastError() {
      $e= xp::$registry['errors'];
      return isset($e[__FILE__]) ? key(end($e[__FILE__])) : 'unknown error';
    }
    
    /**
     * Returns whether a connection has been established
     *
     * @return  bool connected
     */
    public function isConnected() {
      return is_resource($this->_sock);
    }

    /**
     * Clone method. Ensure reconnect
     *
     */
    public function __clone() {
      if (!$this->isConnected()) return;
      $this->close();
      $this->connect();
    }
    
    /**
     * Connect
     *
     * @param   float timeout default 2.0
     * @see     php://fsockopen
     * @return  bool success
     * @throws  peer.ConnectException
     */
    public function connect($timeout= 2.0) {
      if ($this->isConnected()) return TRUE;
      
      if (!$this->_sock= fsockopen(
        $this->_prefix.$this->host,
        $this->port,
        $errno,
        $errstr,
        $timeout
      )) {
        throw new ConnectException(sprintf(
          'Failed connecting to %s:%s within %s seconds [%d: %s]',
          $this->host,
          $this->port,
          $timeout,
          $errno,
          $errstr
        ));
      }
      
      socket_set_timeout($this->_sock, $this->_timeout);
      return TRUE;
    }

    /**
     * Close socket
     *
     * @return  bool success
     */
    public function close() {
      if (!is_resource($this->_sock)) return FALSE;

      $res= fclose($this->_sock);
      $this->_sock= NULL;
      return $res;
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
        socket_set_timeout($this->_sock, $this->_timeout);
      }
    }

    /**
     * Get timeout
     *
     * @return  mixed
     */
    public function getTimeout() {
      return $this->_timeout;
    }

    /**
     * Set socket blocking
     *
     * @param   bool blocking
     * @return  bool success TRUE to indicate the call succeeded
     * @throws  peer.SocketException
     * @see     php://socket_set_blocking
     */
    public function setBlocking($blockMode) {
      if (FALSE === socket_set_blocking($this->_sock, $blockMode)) {
        throw new SocketException('Set blocking call failed: '.$this->getLastError());
      }
      
      return TRUE;
    }
    
    /**
     * Returns whether there is data that can be read
     *
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
      
      $r= array($this->_sock); $w= NULL; $e= NULL;
      $n= stream_select($r, $w, $e, $tv_sec, $tv_usec);
      if (FALSE === $n || NULL === $n || xp::errorAt(__FILE__, __LINE__ - 1)) {
        throw new SocketException('Select failed: '.$this->getLastError());
      }
      
      return $n > 0 ? TRUE : !empty($r);
    }

    /**
     * Reading helper function
     *
     * @param   int maxLen
     * @param   int type ignored
     * @param   bool chop
     * @return  string data
     */
    protected function _read($maxLen, $type, $chop= FALSE) {
      $res= fgets($this->_sock, $maxLen);
      if (FALSE === $res || NULL === $res) {

        // fgets returns FALSE on eof, this is particularily dumb when 
        // looping, so check for eof() and make it "no error"
        if (feof($this->_sock)) return NULL;
        
        $m= stream_get_meta_data($this->_sock);
        if ($m['timed_out']) {
          throw new SocketTimeoutException('Read of '.$maxLen.' bytes failed', $this->_timeout);
        } else {
          throw new SocketException('Read of '.$maxLen.' bytes failed: '.$this->getLastError());
        }
      } else {
        return $chop ? chop($res) : $res;
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
      return $this->_read($maxLen, -1, FALSE);
    }

    /**
     * Read line from a socket
     *
     * @param   int maxLen maximum bytes to read
     * @return  string data
     * @throws  peer.SocketException
     */
    public function readLine($maxLen= 4096) {
      return $this->_read($maxLen, -1, TRUE);
    }

    /**
     * Read data from a socket (binary-safe)
     *
     * @param   int maxLen maximum bytes to read
     * @return  string data
     * @throws  peer.SocketException
     */
    public function readBinary($maxLen= 4096) {
      $res= fread($this->_sock, $maxLen);
      if (FALSE === $res || NULL === $res) {
        throw new SocketException('Read of '.$maxLen.' bytes failed: '.$this->getLastError());
      } else if ('' === $res) {
        $m= stream_get_meta_data($this->_sock);
        if ($m['timed_out']) {
          throw new SocketTimeoutException('Read of '.$maxLen.' bytes failed: '.$this->getLastError(), $this->_timeout);
        }
      }
      
      return $res;
    }
    
    /**
     * Checks if EOF was reached
     *
     * @return  bool
     */
    public function eof() {
      return feof($this->_sock);
    }
    
    /**
     * Write a string to the socket
     *
     * @param   string str
     * @return  int bytes written
     * @throws  peer.SocketException in case of an error
     */
    public function write($str) {
      if (FALSE === ($bytesWritten= fputs($this->_sock, $str, $len= strlen($str)))) {
        throw new SocketException('Write of '.$len.' bytes to socket failed: '.$this->getLastError());
      }
      
      return $bytesWritten;
    }

    /**
     * Retrieve socket handle
     *
     * @return  resource
     */
    public function getHandle() {
      return $this->_sock;
    }

    /**
     * Retrieve input stream
     *
     * @return  io.streams.InputStream
     */
    public function getInputStream() {
      return new SocketInputStream($this);
    }

    /**
     * Retrieve output stream
     *
     * @return  io.streams.OutputStream
     */
    public function getOutputStream() {
      return new SocketOutputStream($this);
    }
    
    /**
     * Destructor
     *
     */
    public function __destruct() {
      if ($this->isConnected()) $this->close();
    }
  }
?>
