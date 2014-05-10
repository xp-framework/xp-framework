<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('peer.BSDSocket', 'peer.SocketImpl');

  /**
   * BSD server socket implementation
   *
   * @ext  sockets
   * @see  xp://peer.BSDSocket
   */
  class BSDSocketImpl extends SocketImpl {
    protected $_eof= FALSE;
    protected $rq= '';
    protected $connected= FALSE;

    static function __static() {
    }

    /**
     * Constructor
     *
     * @param   int domain default AF_INET (one of AF_INET or AF_UNIX)
     * @param   int type default SOCK_STREAM (one of SOCK_STREAM | SOCK_DGRAM | SOCK_RAW | SOCK_SEQPACKET | SOCK_RDM)
     * @param   int protocol default SOL_TCP (one of SOL_TCP or SOL_UDP)
     */
    public function __construct($domain, $type, $protocol) {
      static $domains= array(
         AF_INET   => 'AF_INET',
         AF_INET6  => 'AF_INET6',
         AF_UNIX   => 'AF_UNIX'
      );
      static $types= array(
        SOCK_STREAM     => 'SOCK_STREAM',
        SOCK_DGRAM      => 'SOCK_DGRAM',
        SOCK_RAW        => 'SOCK_RAW',
        SOCK_SEQPACKET  => 'SOCK_SEQPACKET',
        SOCK_RDM        => 'SOCK_RDM'
      );

      if (!is_resource($this->handle= socket_create($domain, $type, $protocol))) {
        throw new SocketException(sprintf(
          'Creating %s socket (type %s, protocol %s) failed: %s',
          $domains[$domain],
          $types[$type],
          getprotobynumber($this->protocol),
          socket_strerror(socket_last_error())
        ));
      }
    }

    /**
     * Bind a given address and port, with a given backlog
     *
     * @param   string address
     * @param   int port
     * @param   int backlog
     * @param   bool reuse
     */
    public function bind($address, $port= 0, $backlog= 10, $reuse= TRUE) {
      if (
        (FALSE === socket_setopt($this->handle, SOL_SOCKET, SO_REUSEADDR, $reuse)) ||
        (FALSE === socket_bind($this->handle, $address, $port))
      ) {
        throw new SocketException(sprintf(
          'Binding socket to '.$address.':'.$port.' failed: %s',
          socket_strerror(socket_last_error($this->handle))
        ));
      }
      
      socket_getsockname($this->handle, $this->host, $this->port);
      if (FALSE === socket_listen($this->handle, $backlog)) {
        throw new SocketException(sprintf(
          'Listening on socket failed: %s',
          socket_strerror(socket_last_error($this->handle))
        ));
      }

      $this->connected= TRUE;
    }

    /**
     * Attach this socket to an existing handle
     *
     * @param  var handle a socket resource
     * @return void
     */
    public function attach($handle) {
      parent::attach($handle);
      $this->connected= TRUE;
    }

    /**
     * Helper method to split up seconds into sec and usec values
     *
     * @param   double seconds
     * @return  [:int]
     */
    protected function timeval($seconds) {
      $sec= floor($seconds);
      $usec= ($seconds- $sec) * 100000;
      return array('sec' => $sec, 'usec' => $usec);
    }

    /**
     * Opens connection
     *
     * @param   string address
     * @param   int port
     * @param   float timeout default 2.0
     * @throws  peer.ConnectException
     */
    public function connect($address, $port= 0, $timeout= 2.0) {
      $host= gethostbyname($address);

      // Use SNDTIMEO as timeout while connecting, then reset it back afterwards
      // to the default read timeout.
      socket_set_option($this->handle, SOL_SOCKET, SO_SNDTIMEO, $this->timeval($timeout));
      $r= socket_connect($this->handle, $host, $port);
      socket_set_option(SOL_SOCKET, SO_SNDTIMEO, $this->timeval($this->timeout));

      if (FALSE === $r) {
        $e= new ConnectException(sprintf(
          'Connect to %s:%d failed: %s',
          $address,
          $port,
          socket_strerror(socket_last_error($this->handle))
        ));
        xp::gc(__FILE__);
        throw $e;
      }

      $this->connected= TRUE;
    }

    /**
     * Returns whether this socket is connected
     *
     * @return  bool
     */
    public function connected() {
      return $this->connected;
    }

    /**
     * Returns local endpoint
     *
     * @return  string
     */
    public function local() {
      if (FALSE === socket_getsockname($this->handle, $host, $port)) {
        throw new SocketException('Cannot get socket name on '.$this->handle);
      }
      return $host.':'.$port;
    }

    /**
     * Sets timeout
     *
     * @param  double timeout
     * @return void
     */
    public function timeout($timeout) {
      parent::timeout($timeout);
      socket_set_option(SOL_SOCKET, SO_RCVTIMEO, $this->timeval($timeout));
      socket_set_option(SOL_SOCKET, SO_SNDTIMEO, $this->timeval($timeout));
    }

    /**
     * Sets a socket option
     *
     * @param  int level
     * @param  int name
     * @param  int value
     * @return void
     */
    public function option($level, $name, $value) {
      socket_set_option($this->handle, $level, $name, $value);
    }

    /**
     * Accepts an incoming client connection
     *
     * @return  peer.Socket
     * @throws  peer.SocketException
     */
    public function accept() {
      if (0 > ($accepted= socket_accept($this->handle))) {
        throw new SocketException(sprintf(
          'Accept failed: %s',
          socket_strerror(socket_last_error($this->handle))
        ));
      }
      if (!is_resource($accepted)) return FALSE;
      
      // Get peer
      if (FALSE === socket_getpeername($accepted, $host, $port)) {
        throw new SocketException(sprintf(
          'Cannot get peer: %s',
          socket_strerror(socket_last_error($accepted))
        ));      
      }

      return new Socket($host, $port, $accepted, new XPClass(__CLASS__));
    }

    /**
     * Set whether to be block
     *
     * @param   bool mode
     * @return  void
     */
    public function block($mode) {
      if ($mode) {
        $ret= socket_set_block($this->handle);
      } else {
        $ret= socket_set_nonblock($this->handle);
      }
      if (FALSE === $ret) {
        $e= new SocketException('Failed to set '.($mode ? 'blocking' : 'nonblocking'));
        xp::gc(__FILE__);
        throw $e;
      }
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
      if (NULL === $timeout) {
        $tv_sec= $tv_usec= NULL;
      } else {
        $tv_sec= (int)floor($timeout);
        $tv_usec= (int)(($timeout- $tv_sec) * 1000000);
      }

      if (FALSE === ($n= socket_select($r, $w, $e, $tv_sec, $tv_usec))) {
        $e= new SocketException('Select failed: '.socket_strerror(socket_last_error()));
        xp::gc(__FILE__);
        throw $e;
      }
      return $n;
    }

    /**
     * Reading helper
     *
     * @param   int maxLen
     * @return  string data
     */
    protected function _read($maxLen) {
      $res= '';
      if (!$this->_eof && 0 === strlen($this->rq)) {
        if (!$this->_select(array($this->handle), NULL, NULL, $this->timeout)) {
          $e= new SocketTimeoutException('Read of '.$maxLen.' bytes failed', $this->timeout);
          xp::gc(__FILE__);
          throw $e;
        }
        $res= @socket_read($this->handle, $maxLen);
        if (FALSE === $res || NULL === $res) {
          $error= socket_last_error($this->handle);
          if (0 === $error || SOCKET_ECONNRESET === $error) {
            $this->_eof= TRUE;
            return '';
          }
          $e= new SocketException('Read of '.$maxLen.' bytes failed');
          xp::gc(__FILE__);
          throw $e;
        } else if ('' === $res) {
          $this->_eof= TRUE;
        }
      }

      return $this->rq.$res;
    }

    /**
     * Reads a line
     *
     * @param   int length
     * @return  string
     */
    public function gets($length) {
      if ('' === ($read= $this->_read($length))) return NULL;
      $c= strcspn($read, "\n");
      $this->rq= substr($read, $c+ 1);
      $chunk= substr($read, 0, $c+ 1);
      return $chunk;
    }

    /**
     * Reads a chunk
     *
     * @param   int length
     * @return  string
     */
    public function read($length) {
      if ('' === ($read= $this->_read($length))) return NULL;
      $this->rq= substr($read, $maxLen);
      return substr($read, 0, $maxLen);
    }

    /**
     * Writes a chunk
     *
     * @param   string
     * @return  int
     */
    public function write($bytes) {
      $length= strlen($bytes);
      if (FALSE === ($written= socket_write($this->handle, $bytes, $length))) {
        $e= new SocketException('Write of '.$length.' bytes to socket failed');
        xp::gc(__FILE__);
        throw $e;
      }
      
      return $written;
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
      if (NULL === $timeout) {
        $tv_sec= $tv_usec= NULL;
      } else {
        $tv_sec= (int)floor($timeout);
        $tv_usec= (int)(($timeout- $tv_sec) * 1000000);
      }

      do {
        $socketSelectInterrupted = FALSE;
        if (FALSE === ($n= socket_select($s->handles[0], $s->handles[1], $s->handles[2], $tv_sec, $tv_usec))) {
          $l= __LINE__ - 1;

          // If socket_select has been interrupted by a signal, it will return FALSE,
          // but no actual error occurred - so check for "real" errors before throwing
          // an exception. If no error has occurred, skip over to the socket_select again.
          if (0 !== ($error= socket_last_error()) || xp::errorAt(__FILE__, $l)) {
            socket_clear_error();
            $e= new SocketException(sprintf('Select failed - #%d: %s', $error, socket_strerror($error)));
            xp::gc(__FILE__);
            throw $e;
          } else {
            $socketSelectInterrupted = TRUE;
          }
        }

      // if socket_select was interrupted by signal, retry socket_select
      } while ($socketSelectInterrupted);
      return $n;
    }

    /**
     * Closes underlying socket
     *
     * @return  void
     */
    public function close() {
      if ($this->handle) {
        socket_close($this->handle);
        $this->_eof= FALSE;
      }
      $this->connected= FALSE;
    }
  }
?>