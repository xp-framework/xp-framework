<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'peer.server.ConnectionEvent',
    'peer.server.ConnectionListener'
  );

  /**
   * ConnectionListener wrapper protocol 
   *
   * @see      xp://peer.server.Server#addListener
   * @purpose  BC Wrapper 
   */
  class ListenerWrapperProtocol extends Object {
    var
      $listeners= array();

    /**
     * Add a connection listener
     *
     * @access  public
     * @param   &peer.server.ConnectionListener listener
     */
    function addListener(&$listener) {      
      $this->listeners[]= &$listener;
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
     * Handle client connect
     *
     * @access  public
     * @param   &peer.Socket
     */
    function handleConnect(&$socket) {
      $this->notify(new ConnectionEvent(EVENT_CONNECTED, $socket));
    }

    /**
     * Handle client disconnect
     *
     * @access  public
     * @param   &peer.Socket
     */
    function handleDisconnect(&$socket) {
       $this->notify(new ConnectionEvent(EVENT_DISCONNECTED, $socket));
     }
  
    /**
     * Handle client data
     *
     * @access  public
     * @param   &peer.Socket
     * @return  mixed
     */
    function handleData(&$socket) { 
      try(); {
        if (NULL === ($data= $socket->readBinary())) throw(new IOException('EOF'));
      } if (catch('IOException', $e)) {
        return throw($e);
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
    function handleError(&$socket, &$e) {
      $this->notify(new ConnectionEvent(EVENT_ERROR, $socket, $e));
    }

  } implements(__FILE__, 'peer.server.ServerProtocol');
?>
