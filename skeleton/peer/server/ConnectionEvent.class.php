<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Connection event
   *
   * @deprecated Implement peer.protocol.ServerProtocol instead!
   * @see      xp://peer.server.Server#service
   * @purpose  Event
   */
  class ConnectionEvent extends Object {
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
