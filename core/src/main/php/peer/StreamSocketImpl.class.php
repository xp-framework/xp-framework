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
    protected $handle= NULL;
    protected $host;
    protected $port;

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

      return new Socket($host, $port, $accepted);
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
      return Socket::select($s, $timeout);
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