<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'peer.ServerSocket', 
    'peer.server.ConnectionEvent',
    'peer.server.ConnectionListener'
  );

  /**
   * Basic TCP/IP Server
   *
   * <code>
   *   uses('peer.server.Server');
   *   
   *   $server= &new Server('127.0.0.1', 6100);
   *   $server->addListener(new ConnectionListener());
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
    var
      $socket     = NULL,
      $terminate  = FALSE,
      $tcpnodelay = FALSE;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string addr
     * @param   int port
     */
    function __construct($addr, $port) {
      $this->socket= &new ServerSocket($addr, $port);
    }
    
    /**
     * Initialize the server
     *
     * @access  public
     */
    function init() {
      $this->socket->create();
      $this->socket->bind(TRUE);
      $this->socket->listen();
    }
    
    /**
     * Shutdown the server
     *
     * @access  public
     */
    function shutdown() {
      $this->server->terminate= TRUE;
      $this->socket->close();
      $this->server->terminate= FALSE;
    }
    
    /**
     * Add a connection listener
     *
     * @access  public
     * @param   &peer.server.ConnectionListener listener
     * @return  &peer.server.ConnectionListener the added listener
     */
    function &addListener(&$listener) {
      $listener->server= &$this;
      $this->listeners[]= &$listener;
      return $listener;
    }

    /**
     * Set TCP_NODELAY
     *
     * @access  public
     * @param   bool tcpnodelay
     */
    function setTcpnodelay($tcpnodelay) {
      $this->tcpnodelay= $tcpnodelay;
    }

    /**
     * Get TCP_NODELAY
     *
     * @access  public
     * @return  bool
     */
    function getTcpnodelay() {
      return $this->tcpnodelay;
    }
    
    /**
     * Notify listeners
     *
     * @access  protected
     * @param   &peer.server.ConnectionEvent event
     */
    function notify(&$event) {
      for ($i= 0, $s= sizeof($this->listeners); $i < $s; $i++) {
        $this->listeners[$i]->{$event->type}($event);
      }
    }
    
    /**
     * Service
     *
     * @access  public
     */
    function service() {
      if (!$this->socket->isConnected()) return FALSE;

      $null= NULL;
      $handles= array();
      $accepting= $this->socket->getHandle();
      
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
            $this->notify(new ConnectionEvent(EVENT_DISCONNECTED, $handles[$h]));
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
          return throw(new SocketException('Call to select() failed'));
        }

        foreach ($read as $i => $handle) {

          // If there is data on the server socket, this means we have a new client.
          // In case the accept() call fails, break out of the loop and terminate
          // the server - this really should not happen!
          if ($handle === $accepting) {
            if (!($m= &$this->socket->accept())) {
              return throw(new SocketException('Call to accept() failed'));
            }
            
            $this->tcpnodelay && $m->setOption($tcp, TCP_NODELAY, TRUE);
            $this->notify(new ConnectionEvent(EVENT_CONNECTED, $m));
            $handles[(int)$m->getHandle()]= &$m;
            continue;
          }
          
          // Otherwise, a client is sending data: read it and notify the listeners.
          // In case of an I/O error, close the client socket and remove the client
          // from the list.
          $index= (int)$handle;
          try(); {
            $data= $handles[$index]->readBinary();
          } if (catch('IOException', $e)) {
            $this->notify(new ConnectionEvent(EVENT_ERROR, $handles[$index], $e));
            $handles[$index]->close();
            unset($handles[$index]);
            continue;
          }
          
          // Check if we got an EOF from the client - in this file the connection
          // was gracefully closed.
          if ($handles[$index]->eof()) {
            $this->notify(new ConnectionEvent(EVENT_DISCONNECTED, $handles[$h]));
            $handles[$index]->close();
            unset($handles[$index]);
            continue;
          }
          
          $this->notify(new ConnectionEvent(EVENT_DATA, $handles[$index], $data));
        }
      }
    }
  }
?>
