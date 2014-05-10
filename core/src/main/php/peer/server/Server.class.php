<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'peer.ServerSocket', 
    'peer.server.ServerProtocol', 
    'peer.server.protocol.SocketAcceptHandler',
    'peer.server.protocol.OutOfResourcesHandler'
  );

  /**
   * Basic TCP/IP Server
   *
   * <code>
   *   uses('peer.server.Server');
   *   
   *   $server= new Server('127.0.0.1', 6100);
   *   $server->setProtocol(new MyProtocol());
   *   try {
   *     $server->init();
   *     $server->service();
   *     $server->shutdown();
   *   } catch(XPException $e) {
   *     $e->printStackTrace();
   *     exit(-1);
   *   }
   * </code>
   *
   * @ext   sockets
   * @see   xp://peer.ServerSocket
   * @test  xp://net.xp_framework.unittest.peer.server.ServerTest
   */
  class Server extends Object {
    public
      $protocol   = NULL,
      $socket     = NULL,
      $server     = NULL,
      $terminate  = FALSE,
      $tcpnodelay = FALSE;

    /**
     * Constructor
     *
     * @param   var arg either a ServerSocket instance or an address
     * @param   int port
     */
    public function __construct($arg, $port= NULL) {
      if ($arg instanceof ServerSocket) {
        $this->socket= $arg;
      } else {
        $this->socket= new ServerSocket($arg, $port);
      }
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
      $handles= $lastAction= array();
      $this->protocol->initialize();

      // Loop
      $tcp= getprotobyname('tcp');
      $timeout= NULL;
      while (!$this->terminate) {
        xp::gc();

        // Build array of sockets that we want to check for data. If one of them
        // has disconnected in the meantime, notify the listeners (socket will be
        // already invalid at that time) and remove it from the clients list.
        $read= array($this->socket);
        $currentTime= time();
        foreach ($handles as $h => $handle) {
          if (!$handle->isConnected()) {
            $this->protocol->handleDisconnect($handle);
            unset($handles[$h]);
            unset($lastAction[$h]);
          } else if ($currentTime - $lastAction[$h] > $handle->getTimeout()) {
            $this->protocol->handleError($handle, new SocketTimeoutException('Timed out', $handle->getTimeout()));
            $handle->close();
            unset($handles[$h]);
            unset($lastAction[$h]);
          } else {
            $read[]= $handle;
          }
        }

        // Check to see if there are sockets with data on it. In case we can
        // find some, loop over the returned sockets. In case the select() call
        // fails, break out of the loop and terminate the server - this really 
        // should not happen!
        $test= new Sockets($read, NULL, NULL);
        $this->socket->select($test, $timeout);
        foreach ($test->read() as $socket) {

          // If there is data on the server socket, this means we have a new client.
          // In case the accept() call fails, break out of the loop and terminate
          // the server - this really should not happen!
          if ($socket->equals($this->socket)) {
            if (!($m= $socket->accept())) {
              throw new SocketException('Call to accept() failed');
            }

            // Handle accepted socket
            if ($this->protocol instanceof SocketAcceptHandler) {
              if (!$this->protocol->handleAccept($m)) {
                $m->close();
                continue;
              }
            }
            
            $this->tcpnodelay && $m->setOption($tcp, TCP_NODELAY, TRUE);
            $this->protocol->handleConnect($m);
            $index= (int)$m->getHandle();
            $handles[$index]= $m;
            $lastAction[$index]= $currentTime;
            $timeout= $m->getTimeout();
            continue;
          }
          
          // Otherwise, a client is sending data. Let the protocol decide what do
          // do with it. In case of an I/O error, close the client socket and remove 
          // the client from the list.
          $index= (int)$socket->getHandle();
          $lastAction[$index]= $currentTime;
          try {
            $this->protocol->handleData($socket);
          } catch (IOException $e) {
            $this->protocol->handleError($socket, $e);
            $socket->close();
            unset($handles[$index]);
            unset($lastAction[$index]);
            continue;
          }
          
          // Check if we got an EOF from the client - in this file the connection
          // was gracefully closed.
          if (!$socket->isConnected() || $socket->eof()) {
            $this->protocol->handleDisconnect($socket);
            $socket->close();
            unset($handles[$index]);
            unset($lastAction[$index]);
          }
        }
      }
    }
  }
?>
