<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('lang.Closeable');

  /**
   * Abstract base class for all other socket implementations
   *
   * @see  xp://peer.BSDSocketImpl
   * @see  xp://peer.StreamSocketImpl
   */
  abstract class SocketImpl extends Object implements Closeable {
    protected $handle= NULL;
    protected $host;
    protected $port;
    protected $timeout= 60.0;

    public static $BSD= NULL;
    public static $STREAM= NULL;

    static function __static() {
      if (extension_loaded('sockets')) {
        self::$BSD= XPClass::forName('peer.BSDSocketImpl');
      } else {
        define('AF_UNIX', 1);
        define('AF_INET', 2);

        define('SOCK_STREAM', 1);
        define('SOCK_DGRAM', 2);
        define('SOCK_RAW', 3);
        define('SOCK_RDM', 4);
        define('SOCK_SEQPACKET', 5);

        define('SOL_TCP', 6);
        define('SOL_UDP', 17);
      }
      self::$STREAM= XPClass::forName('peer.StreamSocketImpl');
    }

    /**
     * Constructor.
     *
     * @param   int domain default AF_INET (one of AF_INET or AF_UNIX)
     * @param   int type default SOCK_STREAM (one of SOCK_STREAM | SOCK_DGRAM | SOCK_RAW | SOCK_SEQPACKET | SOCK_RDM)
     * @param   int protocol default SOL_TCP (one of SOL_TCP or SOL_UDP)
     */
    public abstract function __construct($domain, $type, $protocol);

    /**
     * Bind a given address and port, with a given backlog
     *
     * @param   string address
     * @param   int port
     * @param   int backlog
     * @param   bool reuse
     */
    public abstract function bind($address, $port= 0, $backlog= 10, $reuse= TRUE);

    /**
     * Opens connection
     *
     * @param   string address
     * @param   int port
     * @param   float timeout default 2.0
     * @throws  peer.ConnectException
     */
    public abstract function connect($address, $port= 0, $timeout= 2.0);

    /**
     * Set whether to be block
     *
     * @param   bool mode
     * @return  void
     */
    public abstract function block($mode);

    /**
     * Reads a line
     *
     * @param   int length
     * @return  string
     */
    public abstract function gets($length);

    /**
     * Reads a chunk
     *
     * @param   int length
     * @return  string
     */
    public abstract function read($length);

    /**
     * Writes a chunk
     *
     * @param   string
     * @return  int
     */
    public abstract function write($bytes);

    /**
     * Returns whether this socket is connected
     *
     * @return  bool
     */
    public abstract function connected();

    /**
     * Accepts an incoming client connection
     *
     * @return  peer.Socket
     * @throws  peer.SocketException
     */
    public abstract function accept();

    /**
     * Select
     *
     * @param   peer.Sockets s
     * @param   float timeout default NULL Timeout value in seconds (e.g. 0.5)
     * @return  int
     * @throws  peer.SocketException in case of failure
     */
    public abstract function select(Sockets $s, $timeout= NULL);

    /**
     * Sets timeout
     *
     * @param  double timeout
     * @return void
     */
    public function timeout($timeout) {
      $this->timeout= $timeout;
    }

    /**
     * Attach this socket to an existing handle
     *
     * @param  var handle a socket resource
     * @return void
     */
    public function attach($handle) {
      $this->handle= $handle;
    }

    /**
     * Returns the underlying socket host
     *
     * @return  string
     */
    public function host() {
      return $this->host;
    }

    /**
     * Returns the underlying socket port
     *
     * @return  int
     */
    public function port() {
      return $this->port;
    }

    /**
     * Returns the underlying socket handle
     *
     * @return  var
     */
    public function handle() {
      return $this->handle;
    }

    /**
     * Returns whether a given value is equal to this socket
     *
     * @param  var cmp
     * @return bool
     */
    public function equals($cmp) {
      return $cmp instanceof self && $this->handle === $cmp->handle;
    }

    /**
     * Destructor. Ensure underlying socket is closed.
     */
    public function __destruct() {
      $this->close();
    }
  }
?>