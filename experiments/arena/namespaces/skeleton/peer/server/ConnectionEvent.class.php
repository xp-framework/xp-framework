<?php
/* This class is part of the XP framework
 *
 * $Id: ConnectionEvent.class.php 10594 2007-06-11 10:04:54Z friebe $
 */

  namespace peer::server;

  /**
   * Connection event
   *
   * @deprecated Implement peer.protocol.ServerProtocol instead!
   * @see      xp://peer.server.Server#service
   * @purpose  Event
   */
  class ConnectionEvent extends lang::Object {
    public
      $type     = '',
      $stream   = NULL,
      $data     = NULL;
      
    /**
     * Constructor
     *
     * @param   string type
     * @param   peer.Socket stream
     * @param   mixed data default NULL
     */
    public function __construct($type, $stream, $data= NULL) {
      $this->type= $type;
      $this->stream= $stream;
      $this->data= $data;
      
    }
  }
?>
