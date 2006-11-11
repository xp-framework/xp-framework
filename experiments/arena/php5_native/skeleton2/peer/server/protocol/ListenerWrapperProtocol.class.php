<?php
/* This class is part of the XP framework
 *
 * $Id: ListenerWrapperProtocol.class.php 8247 2006-10-24 15:29:13Z friebe $ 
 */

  uses(
    'peer.server.ConnectionEvent',
    'peer.server.ConnectionListener',
    'peer.server.ServerProtocol'
  );

  /**
   * ConnectionListener wrapper protocol 
   *
   * @see      xp://peer.server.Server#addListener
   * @purpose  BC Wrapper 
   */
  class ListenerWrapperProtocol extends Object implements ServerProtocol {
    public
      $listeners= array();

    /**
     * Add a connection listener
     *
     * @access  public
     * @param   &peer.server.ConnectionListener listener
     */
    public function addListener(&$listener) {      
      $this->listeners[]= &$listener;
    }
    
    /**
     * Initialize Protocol
     *
     * @access  public
     * @return  bool
     */
    public function initialize() { }

    /**
     * Notify listeners
     *
     * @access  protected
     * @param   &peer.server.ConnectionEvent event
     */
    public function notify(&$event) {
      for ($i= 0, $s= sizeof($this->listeners); $i < $s; $i++) {
        $this->listeners[$i]->{$event->type}($event);
      }
    }

    /**
     * Handle client connect
     *
     * @access  public
     * @param   &peer.Socket
     */
    public function handleConnect(&$socket) {
      $this->notify(new ConnectionEvent(EVENT_CONNECTED, $socket));
    }

    /**
     * Handle client disconnect
     *
     * @access  public
     * @param   &peer.Socket
     */
    public function handleDisconnect(&$socket) {
       $this->notify(new ConnectionEvent(EVENT_DISCONNECTED, $socket));
     }
  
    /**
     * Handle client data
     *
     * @access  public
     * @param   &peer.Socket
     * @return  mixed
     */
    public function handleData(&$socket) { 
      try {
        if (NULL === ($data= $socket->readBinary())) throw(new IOException('EOF'));
      } catch (IOException $e) {
        throw($e);
      }

      $this->notify(new ConnectionEvent(EVENT_DATA, $socket, $data));
    }

    /**
     * Handle I/O error
     *
     * @access  public
     * @param   &peer.Socket
     * @param   &lang.Exception e
     */
    public function handleError(&$socket, &$e) {
      $this->notify(new ConnectionEvent(EVENT_ERROR, $socket, $e));
    }

  } 
?>
