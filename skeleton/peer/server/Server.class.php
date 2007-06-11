<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('peer.ServerSocket');

  /**
   * Basic TCP/IP Server
   *
   * <code>
   *   uses('peer.server.Server');
   *   
   *   $server= &new Server('127.0.0.1', 6100);
   *   $server->setProtocol(new MyProtocol());
   *   try(); {
   *     $server->init();
   *     $server->service();
   *     $server->shutdown();
   *   } if (catch('Exception', $e)) {
   *     $e->printStackTrace();
   *     exit(-1);
   *   }
   * </code>
   *
   * @ext      sockets
   * @see      xp://peer.ServerSocket
   * @purpose  TCP/IP Server
   */
  class Server extends Object {
    public
      $protocol   = NULL,
      $socket     = NULL,
      $terminate  = FALSE,
      $tcpnodelay = FALSE;
      
    /**
     * Constructor
     *
     * @param   string addr
     * @param   int port
     */
    public function __construct($addr, $port) {
      $this->socket= new ServerSocket($addr, $port);
    }
    
    /**
     * Initialize the server
     *
     */
    public function init() {
      $this->socket->create();
      $this->socket->bind(TRUE);
      $this->socket->listen();
    }
    
    /**
     * Shutdown the server
     *
     */
    public function shutdown() {
      $this->server->terminate= TRUE;
      $this->socket->close();
      $this->server->terminate= FALSE;
    }
    
    /**
     * Add a connection listener. Provided for BC reasons.
     *
     * @deprecated Use setProtocol() instead!
     * @param   peer.server.ConnectionListener listener
     * @return  peer.server.ConnectionListener the added listener
     */
    public function addListener($listener) {
      if (!$this->protocol) {
        $c= XPClass::forName('peer.server.protocol.ListenerWrapperProtocol');
        $this->protocol= $c->newInstance();
      }

      $listener->server= $this;
      $this->protocol->addListener($listener);
      return $listener;
    }

    /**
     * Sets this server's protocol
     *
     * @param   peer.server.ServerProtocol protocol
     * @return  peer.server.ServerProtocol protocol
     */
    public function setProtocol($protocol) {
      $protocol->server= $this;
      $this->protocol= $protocol;
      return $protocol;
    }

    /**
     * Set TCP_NODELAY
     *
     * @param   bool tcpnodelay
     */
    public function setTcpnodelay($tcpnodelay) {
      $this->tcpnodelay= $tcpnodelay;
    }

    /**
     * Get TCP_NODELAY
     *
     * @return  bool
     */
    public function getTcpnodelay() {
      return $this->tcpnodelay;
    }
    
    /**
     * Service
     *
     */
    public function service() {
      if (!$this->socket->isConnected()) return FALSE;

      $null= NULL;
      $handles= array();
      $accepting= $this->socket->getHandle();
      $this->protocol->initialize();

      // Loop
      $tcp= getprotobyname('tcp');
      while (!$this->terminate) {
        xp::gc();

        // Build array of sockets that we want to check for data. If one of them
        // has disconnected in the meantime, notify the listeners (socket will be
        // already invalid at that time) and remove it from the clients list.
        $read= array($this->socket->_sock);
        foreach (array_keys($handles) as $h) {
          if (!$handles[$h]->isConnected()) {
            $this->protocol->handleDisconnect($handles[$h]);
            unset($handles[$h]);
          } else {
            $read[]= $handles[$h]->getHandle();
          }
        }

        // Check to see if there are sockets with data on it. In case we can
        // find some, loop over the returned sockets. In case the select() call
        // fails, break out of the loop and terminate the server - this really 
        // should not happen!
        if (FALSE === socket_select($read, $null, $null, NULL)) {
          throw(new SocketException('Call to select() failed'));
        }

        foreach ($read as $i => $handle) {

          // If there is data on the server socket, this means we have a new client.
          // In case the accept() call fails, break out of the loop and terminate
          // the server - this really should not happen!
          if ($handle === $accepting) {
            if (!($m= $this->socket->accept())) {
              throw(new SocketException('Call to accept() failed'));
            }
            
            $this->tcpnodelay && $m->setOption($tcp, TCP_NODELAY, TRUE);
            $this->protocol->handleConnect($m);
            $handles[(int)$m->getHandle()]= $m;
            continue;
          }
          
          // Otherwise, a client is sending data. Let the protocol decide what do
          // do with it. In case of an I/O error, close the client socket and remove 
          // the client from the list.
          $index= (int)$handle;
          try {
            $this->protocol->handleData($handles[$index]);
          } catch (IOException $e) {
            $this->protocol->handleError($handles[$index], $e);
            $handles[$index]->close();
            unset($handles[$index]);
            continue;
          }
          
          // Check if we got an EOF from the client - in this file the connection
          // was gracefully closed.
          if ($handles[$index]->eof()) {
            $this->protocol->handleDisconnect($handles[$h]);
            $handles[$index]->close();
            unset($handles[$index]);
          }
        }
      }
    }
  }
?>
