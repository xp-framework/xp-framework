<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('peer.Socket', 'peer.SocketImpl');

  /**
   * Stream socket implementation
   *
   * @see  xp://peer.Socket
   */
  class StreamSocketImpl extends SocketImpl {
    protected $context= NULL;
    protected $protocol;

    static function __static() {
    }

    /**
     * Constructor
     *
     * @param   int domain default AF_INET (one of AF_INET or AF_UNIX)
     * @param   int type default SOCK_STREAM (one of SOCK_STREAM, SOCK_DGRAM, SOCK_RAW, SOCK_RDM or SOCK_SEQPACKET)
     * @param   int protocol default SOL_TCP (one of SOL_TCP or SOL_UDP)
     */
    public function __construct($domain, $type, $protocol) {
      static $protocols= array(
        SOL_TCP => 'tcp://%s:%d',
        SOL_UDP => 'udp://%s:%d'
      );

      if (AF_INET === $domain) {
        if (!isset($protocols[$protocol])) {
          throw new IllegalArgumentException('Unknown protocol '.$protocol);
        }
        $this->protocol= $protocols[$protocol];
      } else if (AF_UNIX === $domain) {
        $this->protocol= 'unix://%s';
      } else {
        throw new IllegalArgumentException('Unknown domain '.$doman);
      }

      $this->context= stream_context_create();
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
      $sock= sprintf($this->protocol, $address, $port);
      $this->handle= stream_socket_server(
        $sock,
        $errno,
        $errstr,
        STREAM_SERVER_BIND | STREAM_SERVER_LISTEN,
        $this->context
      );
      if (0 !== $errno) {
        throw new SocketException(sprintf(
          'Binding socket to %s failed: #%d: %s',
          $sock,
          $errno,
          $errstr
        ));
      }

      $bound= stream_socket_get_name($this->handle, FALSE);
      if ('[' === $bound{0}) {
        sscanf($bound, '[%[0-9a-fA-F:]]:%d', $this->host, $this->port);
      } else {
        sscanf($bound, '%[^:]:%d', $this->host, $this->port);
      }
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
      $sock= sprintf($this->protocol, $address, $port);
      $this->handle= stream_socket_client(
        $sock,
        $errno,
        $errstr,
        $timeout,
        STREAM_CLIENT_CONNECT,
        $this->context
      );
      if (0 !== $errno || !$this->handle) {
        $e= new ConnectException(sprintf(
          'Failed connecting to %s within %s seconds [%d: %s]',
          $sock,
          $timeout,
          $errno,
          $errstr
        ));
        xp::gc(__FILE__);
        throw $e;
      }

      $connect= stream_socket_get_name($this->handle, FALSE);
      if ('[' === $connect{0}) {
        sscanf($connect, '[%[0-9a-fA-F:]]:%d', $this->host, $this->port);
      } else {
        sscanf($connect, '%[^:]:%d', $this->host, $this->port);
      }

      stream_set_timeout($this->handle, $this->timeout);
    }

    /**
     * Returns whether this socket is connected
     *
     * @return  bool
     */
    public function connected() {
      return NULL !== $this->handle;
    }

    public function local() {
      if (FALSE === ($addr= stream_socket_get_name($this->handle, FALSE))) {
        throw new SocketException('Cannot get socket name on '.$this->handle);
      }
      return $addr;
    }

    /**
     * Sets timeout
     *
     * @param  double timeout
     * @return void
     */
    public function timeout($timeout) {
      parent::timeout($timeout);

      // If the socket was already open, change it
      if ($this->handle) {
        stream_set_timeout($this->handle, $timeout);
      }
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
      stream_context_set_option($this->context, $level, $name, $value);
    }

    /**
     * Accepts an incoming client connection
     *
     * @return  peer.Socket
     * @throws  peer.SocketException
     */
    public function accept() {
      $accepted= stream_socket_accept($this->handle, -1, $peer);
      if (!is_resource($accepted)) return FALSE;

      if ('[' === $peer{0}) {
        sscanf($peer, '[%[0-9a-fA-F:]]:%d', $host, $port);
      } else {
        sscanf($peer, '%[^:]:%d', $host, $port);
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
      if (FALSE === stream_set_blocking($this->_sock, $mode)) {
        $e= new SocketException('Set blocking call failed: '.$this->getLastError());
        xp::gc(__FILE__);
        throw $e;
      }
    }

    /**
     * Reads a line
     *
     * @param   int length
     * @return  string
     */
    public function gets($length) {
      $r= fgets($this->handle, $length);
      if (FALSE === $r || NULL === $r) {
        if (feof($this->handle)) return NULL;
        $m= stream_get_meta_data($this->handle);
        if ($m['timed_out']) {
          $e= new SocketTimeoutException('Read of '.$length.' bytes failed', $this->timeout);
        } else {
          $e= new SocketException('Read of '.$length.' bytes failed');
        }
        xp::gc(__FILE__);
        throw $e;
      }
      return $r;
    }

    /**
     * Reads a chunk
     *
     * @param   int length
     * @return  string
     */
    public function read($length) {
      $r= fread($this->handle, $length);
      if (FALSE === $r || NULL === $r) {
        $e= new SocketException('Read of '.$length.' bytes failed');
        xp::gc(__FILE__);
        throw $e;
      } else if ('' === $r) {
        $m= stream_get_meta_data($this->handle);
        if ($m['timed_out']) {
          $e= new SocketTimeoutException('Read of '.$length.' bytes failed', $this->timeout);
          xp::gc(__FILE__);
          throw $e;
        }
        return NULL;
      }
      return $r;
    }

    /**
     * Writes a chunk
     *
     * @param   string
     * @return  int
     */
    public function write($bytes) {
      $length= strlen($bytes);
      if (FALSE === ($written= fwrite($this->handle, $bytes, $length))) {
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
        $tv_sec= intval(floor($timeout));
        $tv_usec= intval(($timeout - floor($timeout)) * 1000000);
      }
      $n= stream_select($s->handles[0], $s->handles[1], $s->handles[2], $tv_sec, $tv_usec);
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
        $e= new SocketException('Select('.$s->toString().', '.$tv_sec.', '.$tv_usec.')= failed');
        xp::gc(__FILE__);
        throw $e;
      }

      return $n > 0 ? $n : sizeof($s->handles[0]) + sizeof($s->handles[1]) + sizeof($s->handles[2]);
    }

    /**
     * Closes underlying socket
     *
     * @return  void
     */
    public function close() {
      if ($this->handle) {
        fclose($this->handle);
        $this->handle= NULL;
      }
    }
  }
?>