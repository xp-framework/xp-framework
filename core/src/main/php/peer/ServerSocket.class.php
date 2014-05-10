<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('peer.SocketHandle', 'peer.Sockets');

  /**
   * Server socket interface
   *
   * <code>
   *   $s= new ServerSocket('127.0.0.1', 80);
   *   try {
   *     $s->bind();
   *   } catch (SocketException $e) {
   *     $e->printStackTrace();
   *     $s->close();
   *     exit();
   *   }
   *
   *   while ($m= $s->accept()) {
   *     $buf= $m->read(2048);
   *     $m->write('You said: '.$buf);
   *     $m->close();
   *   }
   *   $s->close();
   * </code>
   */
  class ServerSocket extends Object implements SocketHandle {
    public $host     = '';
    public $protocol = 0;

    protected static $impl = null;

    static function __static() {
      if (extension_loaded('sockets')) {
        self::$impl= XPClass::forName('peer.server.BSDServerSocketImpl');
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
        self::$impl= XPClass::forName('peer.server.StreamServerSocketImpl');
      }
    }

    /**
     * Constructor
     *
     * @param   string host
     * @param   int port
     * @param   int domain default AF_INET (one of AF_INET or AF_UNIX)
     * @param   int type default SOCK_STREAM (one of SOCK_STREAM | SOCK_DGRAM | SOCK_RAW | SOCK_SEQPACKET | SOCK_RDM)
     * @param   int protocol default SOL_TCP (one of SOL_TCP or SOL_UDP)
     */
    public function __construct($host, $port, $domain= AF_INET, $type= SOCK_STREAM, $protocol= SOL_TCP) {
      $this->host= $host;
      $this->port= $port;
      $this->impl= self::$impl->newInstance($domain, $type, $protocol);
    }

    /**
     * Bind and listen on this socket
     *
     * <quote>
     * A maximum of backlog incoming connections will be queued for processing. 
     * If a connection request arrives with the queue full the client may receive an 
     * error with an indication of ECONNREFUSED, or, if the underlying protocol 
     * supports retransmission, the request may be ignored so that retries may 
     * succeed. 
     * </quote>
     *
     * @param   bool reuse default FALSE
     * @param   int backlog default 10
     * @return  bool success
     * @throws  peer.SocketException in case of an error
     */
    public function bind($reuse= FALSE, $backlog= 10) {
      $this->impl->bind($this->host, $this->port, $backlog, $reuse);

      // Update socket host and port (given a ":0" port as parameter,
      // the member variable will now contain the actual port we bound).
      $this->host= $this->impl->host();
      $this->port= $this->impl->port();
      return TRUE;
    }

    /**
     * Accept connection
     *
     * <quote>
     * This function will accept incoming connections on that socket. Once a 
     * successful connection is made, a new socket object is returned, which 
     * may be used for communication. If there are multiple connections queued 
     * on the socket, the first will be used. If there are no pending connections, 
     * socket_accept() will block until a connection becomes present.
     * </quote> 
     *
     * Note: If this socket has been made non-blocking, FALSE will be returned.
     *
     * @return  var a peer.Socket object or FALSE
     * @throws  peer.SocketException in case of an error
     */
    public function accept() {
      return $this->impl->accept();
    }

    /**
     * Returns whether this socket is connected
     *
     * @return  bool
     */
    public function isConnected() {
      return $this->impl->isConnected();
    }

    /**
     * Returns the underlying socket handle
     *
     * @return  var
     */
    public function getHandle() {
      return $this->impl->handle();
    }

    /**
     * Closes this socket
     *
     * @return  void
     */
    public function close() {
      $this->impl->close();
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
     * Create
     *
     * @deprecated Does not need to be called any longer
     * @return  bool success
     * @throws  peer.SocketException in case of an error
     */
    public function create() {
      return TRUE;
    }

    /**
     * Listen on this socket
     *
     * @deprecated Does not need to be called any longer
     * @param   int backlog default 10
     * @return  bool success
     * @throws  peer.SocketException in case of an error
     */
    public function listen($backlog= 10) {
      return TRUE;
    }
  }
?>
