<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'peer.SocketHandle',
    'peer.SocketImpl',
    'peer.ConnectException',
    'peer.SocketTimeoutException',
    'peer.SocketEndpoint',
    'peer.SocketException',
    'peer.SocketInputStream',
    'peer.SocketOutputStream',
    'peer.Sockets'
  );
  
  /**
   * The socket class
   *
   * @test  xp://net.xp_framework.unittest.peer.sockets.SocketTest
   * @see   php://network
   */
  class Socket extends Object implements SocketHandle {
    public $host= '';
    public $port= 0;

    protected $_eof= FALSE;
    protected $_options = array();
    protected $_timeout = 60;
    protected $sol= SOL_TCP;

    /**
     * Constructor
     *
     * Note: When specifying a numerical IPv6 address (e.g. fe80::1)
     * as value for the parameter "host", you must enclose the IP in
     * square brackets.
     *
     * @param   string host hostname or IP address
     * @param   int port
     * @param   var handle default NULL
     * @param   lang.XPClass impl default NULL
     */
    public function __construct($host, $port, $handle= NULL, XPClass $impl= NULL) {
      if (NULL === $impl) $impl= SocketImpl::$STREAM;
      $this->impl= $impl->newInstance(AF_INET, SOCK_STREAM, $this->sol);
      $this->host= $host;
      $this->port= $port;
      if ($handle) {
        $this->impl->attach($handle);
      }
    }

    /**
     * Returns remote endpoint
     *
     * @return  peer.SocketEndpoint
     */
    public function remoteEndpoint() {
      return new SocketEndpoint($this->host, $this->port);
    }

    /**
     * Returns local endpoint
     *
     * @return  peer.SocketEndpoint
     * @throws  peer.SocketException
     */
    public function localEndpoint() {
      if ($this->impl->connected()) {
        return SocketEndpoint::valueOf($this->impl->local());
      } else {
        return NULL;
      }
    }

    /**
     * Set option on socket context
     *
     * @param   string wrapper 'ssl', 'tcp', 'ftp'
     * @param   string option
     * @param   var value
     */
    protected function setSocketOption($wrapper, $option, $value) {
      $this->_options[$wrapper][$option]= $value;
      $this->impl->option($wrapper, $option, $value);
    }
    
    /**
     * Retrieve option on socket context
     *
     * @param   string wrapper
     * @param   string option
     * @param   var
     */
    protected function getSocketOption($wrapper, $option) {
      return isset($this->_options[$wrapper][$option])
        ? $this->_options[$wrapper][$option]
        : NULL
      ;
    }

    /**
     * Get last error. A very inaccurate way of going about error messages since
     * any PHP error/warning is returned - but since there's no function like
     * flasterror() we must rely on this
     *
     * @deprecated
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
      return $this->impl->connected();
    }

    /**
     * Clone method. Ensure reconnect
     */
    public function __clone() {
      if ($this->impl->connected()) {
        $this->close();
        $this->connect();
      }
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
      if (!$this->impl->connected()) {
        $this->impl->connect($this->host, $this->port, $timeout);
      }
      return TRUE;
    }

    /**
     * Close socket
     *
     * @return  bool success
     */
    public function close() {
      if ($this->impl && $this->impl->connected()) {
        $this->impl->close();
        $this->_eof= FALSE;
        return TRUE;
      }
      return FALSE;
    }

    /**
     * Set timeout
     *
     * @param   double timeout
     */
    public function setTimeout($timeout) {
      $this->impl->timeout($timeout);
      $this->_timeout= $timeout;
    }

    /**
     * Get timeout
     *
     * @return  double
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
      $this->impl->block($blockMode);
    }
    
    /**
     * Returns whether there is data that can be read
     *
     * @param   float timeout default NULL Timeout value in seconds (e.g. 0.5)
     * @return  bool there is data that can be read
     * @throws  peer.SocketException in case of failure
     */
    public function canRead($timeout= NULL) {
      return $this->impl->select(new Sockets(array($this)), $timeout) > 0;
    }

    /**
     * Read data from a socket
     *
     * @param   int maxLen maximum bytes to read
     * @return  string data
     * @throws  peer.SocketException
     */
    public function read($maxLen= 4096) {
      if (NULL === ($bytes= $this->impl->gets($maxLen))) {
        $this->_eof= TRUE;
        return NULL;
      }
      return $bytes;
    }

    /**
     * Read line from a socket
     *
     * @param   int maxLen maximum bytes to read
     * @return  string data
     * @throws  peer.SocketException
     */
    public function readLine($maxLen= 4096) {
      if (NULL === ($bytes= $this->impl->gets($maxLen))) {
        $this->_eof= TRUE;
        return NULL;
      }
      return rtrim($bytes, "\r\n");
    }

    /**
     * Read data from a socket (binary-safe)
     *
     * @param   int maxLen maximum bytes to read
     * @return  string data
     * @throws  peer.SocketException
     */
    public function readBinary($maxLen= 4096) {
      if (NULL === ($bytes= $this->impl->read($maxLen))) {
        $this->_eof= TRUE;
        return '';
      }
      return $bytes;
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
      return $this->impl->write($str);
    }

    /**
     * Retrieve socket handle
     *
     * @return  resource
     */
    public function getHandle() {
      return $this->impl->handle();
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
     * Select
     *
     * @param   peer.Sockets s
     * @param   float timeout default NULL Timeout value in seconds (e.g. 0.5)
     * @return  int
     * @throws  peer.SocketException in case of failure
     */
    public function select(Sockets $s, $timeout= NULL) {
      return $this->impl->select($s, $timeout);
    }

    /**
     * Destructor
     */
    public function __destruct() {
      $this->close();
    }

    /**
     * Returns whether a given value is equal to this socket
     *
     * @param  var cmp
     * @return bool
     */
    public function equals($cmp) {
      return $cmp instanceof self && $this->impl->equals($cmp->impl);
    }
    
    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      $handle= $this->impl->handle();
      return sprintf(
        '%s(%s -> %s%s:%d)',
        $this->getClassName(),
        NULL === $handle ? '(closed)' : xp::stringOf($handle),
        $this->_prefix,
        $this->host,
        $this->port
      );
    }
  }
?>
