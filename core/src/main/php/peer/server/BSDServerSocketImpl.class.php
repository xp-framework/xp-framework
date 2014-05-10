<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('peer.BSDSocket', 'peer.server.ServerSocketImpl');

  /**
   * BSD server socket implementation
   *
   * @ext  sockets
   * @see  xp://peer.BSDSocket
   */
  class BSDServerSocketImpl extends ServerSocketImpl {
    protected $handle= NULL;
    protected $host;
    protected $port;

    /**
     * Constructor
     *
     * @param   int domain default AF_INET (one of AF_INET or AF_UNIX)
     * @param   int type default SOCK_STREAM (one of SOCK_STREAM | SOCK_DGRAM | SOCK_RAW | SOCK_SEQPACKET | SOCK_RDM)
     * @param   int protocol default SOL_TCP (one of SOL_TCP or SOL_UDP)
     */
    public function __construct($domain, $type, $protocol) {
      if (!is_resource($this->handle= socket_create($domain, $type, $protocol))) {
        throw new SocketException(sprintf(
          'Creating socket failed: %s',
          socket_strerror(socket_last_error())
        ));
      }
    }

    /**
     * Bind a given host and port, with a given backlog
     *
     * @param   string host
     * @param   int port
     * @param   int backlog
     * @param   bool reuse
     */
    public function bind($host, $port, $backlog= 10, $reuse= TRUE) {
      if (
        (FALSE === socket_setopt($this->handle, SOL_SOCKET, SO_REUSEADDR, $reuse)) ||
        (FALSE === socket_bind($this->handle, $host, $port))
      ) {
        throw new SocketException(sprintf(
          'Binding socket to '.$host.':'.$port.' failed: %s',
          socket_strerror(socket_last_error($this->handle))
        ));
      }
      
      socket_getsockname($this->handle, $this->host, $this->port);

      if (FALSE === socket_listen($this->handle, $backlog)) {
        throw new SocketException(sprintf(
          'Listening on socket failed: %s',
          $this->getLastError()
        ));
      }
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
      
      return new BSDSocket($host, $port, $accepted);
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
      return BSDSocket::select($s, $timeout);
    }

    /**
     * Closes underlying socket
     *
     * @return  void
     */
    public function close() {
      if ($this->handle) {
        socket_close($this->handle);
        $this->handle= NULL;
      }
    }
  }
?>