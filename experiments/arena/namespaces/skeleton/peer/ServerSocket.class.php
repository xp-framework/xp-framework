<?php
/* This class is part of the XP framework
 *
 * $Id: ServerSocket.class.php 10594 2007-06-11 10:04:54Z friebe $
 */

  namespace peer;

  ::uses('peer.BSDSocket');

  /**
   * BSDSocket server implementation
   *
   * <code>
   *   $s= &new ServerSocket('127.0.0.1', 80);
   *   try(); {
   *     $s->create();
   *     $s->bind();
   *     $s->listen();
   *   } if (catch('SocketException', $e)) {
   *     $e->printStackTrace();
   *     $s->close();
   *     exit();
   *   }
   *
   *   while ($m= &$s->accept()) {
   *     $buf= $m->read(2048);
   *     $m->write('You said: '.$buf);
   *     $m->close();
   *   }
   *   $s->close();
   * </code>
   *
   * @purpose  Provide an interface to the BSD sockets                    
   * @see      xp://peer.BSDSocket
   * @ext      sockets                                                    
   */
  class ServerSocket extends BSDSocket {
    public
      $domain   = 0,
      $type     = 0,
      $protocol = 0;
      
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
      $this->domain= $domain;
      $this->type= $type;
      $this->protocol= $protocol;
      parent::__construct($host, $port);
    }
    
    /**
     * Connect. Overwritten method from BSDSocket that will always throw
     * an exception because connect() doesn't make sense here!
     *
     * @return  bool success
     * @throws  lang.IllegalAccessException
     */
    public function connect() {
      throw(new lang::IllegalAccessException('Connect cannot be used on a ServerSocket'));
    }
    
    /**
     * Create
     *
     * @return  bool success
     * @throws  peer.SocketException in case of an error
     */
    public function create() {
      if (!is_resource($this->_sock= socket_create($this->domain, $this->type, $this->protocol))) {
        throw(new SocketException(sprintf(
          'Creating socket failed',
          $this->getLastError()
        )));
      }
      
      return TRUE;
    }
    
    /**
     * Bind
     *
     * @return  bool success
     * @throws  peer.SocketException in case of an error
     */
    public function bind($reuse= FALSE) {
      if (
        (FALSE === socket_setopt($this->_sock, SOL_SOCKET, SO_REUSEADDR, $reuse)) ||
        (FALSE === socket_bind($this->_sock, $this->host, $this->port))
      ) {
        throw(new SocketException(sprintf(
          'Binding socket to '.$this->host.':'.$this->port.' failed',
          $this->getLastError()
        )));
      }
      
      return TRUE;
    }      
    
    /**
     * Listen on this socket
     *
     * <quote>
     * A maximum of backlog incoming connections will be queued for processing. 
     * If a connection request arrives with the queue full the client may receive an 
     * error with an indication of ECONNREFUSED, or, if the underlying protocol 
     * supports retransmission, the request may be ignored so that retries may 
     * succeed. 
     * </quote>
     *
     * @param   int backlog default 10
     * @return  bool success
     * @throws  peer.SocketException in case of an error
     */
    public function listen($backlog= 10) {
      if (FALSE === socket_listen($this->_sock, $backlog)) {
        throw(new SocketException(sprintf(
          'Listening on socket failed',
          $this->getLastError()
        )));
      }
      
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
     * @return  mixed a peer.BSDSocket object or FALSE
     * @throws  peer.SocketException in case of an error
     */
    public function accept() {
      if (0 > ($msgsock= socket_accept($this->_sock))) {
        throw(new SocketException(sprintf(
          'Accept failed',
          $this->getLastError()
        )));
      }
      if (!is_resource($msgsock)) return FALSE;
      
      // Get peer
      if (FALSE === socket_getpeername($msgsock, $host, $port)) {
        throw(new SocketException(sprintf(
          'Cannot get peer',
          $this->getLastError()
        )));      
      }
      
      return new BSDSocket($host, $port, $msgsock);
    }
  }
?>
