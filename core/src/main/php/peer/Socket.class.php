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
      $_eof     = FALSE,
      $host     = '',
      $port     = 0;
      
    public
      $_sock    = NULL,
      $_prefix  = 'tcp://',
      $_timeout = 60;

    private
      $context  = NULL;
    
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
      $this->context= stream_context_create();
    }

    /**
     * Set option on socket context
     *
     * @param   string wrapper 'ssl', 'tcp', 'ftp'
     * @param   string option
     * @param   var value
     */
    protected function setSocketOption($wrapper, $option, $value) {
      stream_context_set_option($this->context, $wrapper, $option, $value);
    }
    
    /**
     * Retrieve option on socket context
     *
     * @param   string wrapper
     * @param   string option
     * @param   var
     */
    protected function getSocketOption($wrapper, $option) {
      $options= stream_context_get_options($this->context);
      return @$options[$wrapper][$option];
    }

    /**
     * Get last error. A very inaccurate way of going about error messages since
     * any PHP error/warning is returned - but since there's no function like
     * flasterror() we must rely on this
     *
     * @return  string error
     */  
    public function getLastError() {
      return isset(xp::$errors[__FILE__]) ? key(end(xp::$errors[__FILE__])) : 'unknown error';
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
      
      if (!$this->_sock= stream_socket_client(
        $this->_prefix.(string)$this->host.':'.$this->port,
        $errno,
        $errstr,
        $timeout,
        STREAM_CLIENT_CONNECT,
        $this->context
      )) {
        $e= new ConnectException(sprintf(
          'Failed connecting to %s:%s within %s seconds [%d: %s]',
          $this->host,
          $this->port,
          $timeout,
          $errno,
          $errstr
        ));
        xp::gc(__FILE__);
        throw $e;
      }
      
      stream_set_timeout($this->_sock, $this->_timeout);
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
      $this->_eof= FALSE;
      return $res;
    }

    /**
     * Set timeout
     *
     * @param   var _timeout
     */
    public function setTimeout($timeout) {
      $this->_timeout= $timeout;
      
      // Apply changes to already opened connection
      if (is_resource($this->_sock)) {
        stream_set_timeout($this->_sock, $this->_timeout);
      }
    }

    /**
     * Get timeout
     *
     * @return  var
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
      if (FALSE === stream_set_blocking($this->_sock, $blockMode)) {
        $e= new SocketException('Set blocking call failed: '.$this->getLastError());
        xp::gc(__FILE__);
        throw $e;
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
        $tv_sec= $tv_usec= NULL;
      } else {
        $tv_sec= intval(floor($timeout));
        $tv_usec= intval(($timeout - floor($timeout)) * 1000000);
      }
      $r= array($this->_sock); $w= NULL; $e= NULL;
      $n= stream_select($r, $w, $e, $tv_sec, $tv_usec);
      $l= __LINE__ -1;
      
      // Implementation vagaries:
      // * For Windows, when using the VC9 binatries, get rid of "Invalid CRT 
      //   parameters detected" warning which is no error, see PHP bug #49948
      // * On Un*x OS flavors, when select() raises a warning, this *is* an 
      //   error (regardless of the return value)
      if (isset(xp::$errors[__FILE__])) {
        if (isset(xp::$errors[__FILE__][$l]['Invalid CRT parameters detected'])) {
          xp::gc(__FILE__);
        } else {
          $n= FALSE;
        }
      }
      
      // OK, real error here now.
      if (FALSE === $n || NULL === $n) {
        $e= new SocketException('Select('.$this->_sock.', '.$tv_sec.', '.$tv_usec.')= failed: '.$this->getLastError());
        xp::gc(__FILE__);
        throw $e;
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
        if (feof($this->_sock)) {
          $this->_eof= TRUE;
          return NULL;
        }
        
        $m= stream_get_meta_data($this->_sock);
        if ($m['timed_out']) {
          $e= new SocketTimeoutException('Read of '.$maxLen.' bytes failed', $this->_timeout);
          xp::gc(__FILE__);
          throw $e;
        } else {
          $e= new SocketException('Read of '.$maxLen.' bytes failed: '.$this->getLastError());
          xp::gc(__FILE__);
          throw $e;
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
        $e= new SocketException('Read of '.$maxLen.' bytes failed: '.$this->getLastError());
        xp::gc(__FILE__);
        throw $e;
      } else if ('' === $res) {
        $m= stream_get_meta_data($this->_sock);
        if ($m['timed_out']) {
          $e= new SocketTimeoutException('Read of '.$maxLen.' bytes failed: '.$this->getLastError(), $this->_timeout);
          xp::gc(__FILE__);
          throw $e;
        }
        $this->_eof= TRUE;
      }
      
      return $res;
    }
    
    /**
     * Checks if EOF was reached
     *
     * @return  bool
     */
    public function eof() {
      return $this->_eof;
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
        $e= new SocketException('Write of '.$len.' bytes to socket failed: '.$this->getLastError());
        xp::gc(__FILE__);
        throw $e;
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
      $this->close();
    }
    
    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        '%s(%s -> %s%s:%d)',
        $this->getClassName(),
        NULL === $this->_sock ? '(closed)' : xp::stringOf($this->_sock),
        $this->_prefix,
        $this->host,
        $this->port
      );
    }
  }
?>
