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
   * (Insert class' description here)
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
   * @ext      extensiom
   * @see      reference
   * @purpose  purpose
   */
  class Server extends Object {
    var
      $socket   = NULL;
      
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function __construct($addr, $port) {
      $this->socket= &new ServerSocket($addr, $port);
      parent::__construct();
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function init() {
      $this->socket->create();
      $this->socket->bind(TRUE);
      $this->socket->listen();
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function shutdown() {
      $this->socket->close();
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function addListener(&$listener) {
      $this->listeners[]= &$listener;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function notify(&$event) {
      printf("Server::notify(%s, %s)\n", $event->type, var_export($event->data, 1));
      
      for ($i= 0, $s= sizeof($this->listeners); $i < $s; $i++) {
        $this->listeners[$i]->{$event->type}($event);
      }
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function service() {
      if (!$this->socket->isConnected()) return FALSE;
      
      while ($m= &$this->socket->accept()) {
        $this->notify(new ConnectionEvent(EVENT_CONNECTED, $m));
        
        // Loop
        do {
          try(); {
            if (NULL === ($data= $m->read())) break;
          } if (catch('IOException', $e)) {
            $this->notify(new ConnectionEvent(EVENT_ERROR, $m, $e));
            break;
          }
          
          // Notify listeners
          $this->notify(new ConnectionEvent(EVENT_DATA, $m, $data));
          
        } while (!$m->eof());
        
        $this->notify(new ConnectionEvent(EVENT_DISCONNECTED, $m));
      }
    }
  }
?>
