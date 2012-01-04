<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_framework.unittest.peer.server.TestingProtocol', 'peer.server.ExtendedServerProtocol');

  /**
   * ExtendedTestingProtocol is exactly like TestingProtocol but
   * implements the ExtendedServerProtocol interface
   *
   */
  class ExtendedTestingProtocol extends net·xp_framework·unittest·peer·server·TestingProtocol implements ExtendedServerProtocol {

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

    /**
     * Handle error
     *
     * @param   peer.Socket socket
     * @param   lang.XPException e
     */
    public function handleOutOfResources($socket, $e) { 
      Console::$err->writeLine('OOR ', $this->hashOf($socket));
    }
  }
?>
