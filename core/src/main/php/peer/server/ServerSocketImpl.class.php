<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('lang.Closeable');

  /**
   * Abstract server socket implementation
   *
   * @see  xp://peer.server.BSDServerSocketImpl
   * @see  xp://peer.server.StreamServerSocketImpl
   */
  abstract class ServerSocketImpl extends Object implements Closeable {
    protected $handle= NULL;
    protected $host;
    protected $port;

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
     * Returns whether this socket is connected
     *
     * @return  bool
     */
    public function isConnected() {
      return NULL !== $this->handle;
    }

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
     * Destructor. Ensure underlying socket is closed.
     */
    public function __destruct() {
      $this->close();
    }
  }
?>