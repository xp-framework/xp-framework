<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_framework.unittest.peer.server.TestingProtocol', 'peer.server.protocol.SocketAcceptHandler');

  /**
   * AcceptTestingProtocol handles socket accepts
   *
   */
  class AcceptTestingProtocol extends net·xp_framework·unittest·peer·server·TestingProtocol implements SocketAcceptHandler {

    /**
     * Handle accept
     *
     * @param   peer.Socket socket
     * @return  bool
     */
    public function handleAccept($socket) { 
      Console::$err->writeLine('ACCEPT ', $this->hashOf($socket));
      return TRUE;
    }
  }
?>
