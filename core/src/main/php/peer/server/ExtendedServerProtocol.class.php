<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.server.ServerProtocol');

  /**
   * Server Protocol
   *
   * @see      xp://peer.server.Server#setProtocol
   */
  interface ExtendedServerProtocol extends ServerProtocol {

    /**
     * Handle accepted socket. Return FALSE to make server drop connection
     * immediately, TRUE to continue on to handleConnect().
     *
     * @param   peer.Socket socket
     * @return  bool
     */
    public function handleAccept($socket);
  
    /**
     * Handle out of resources error
     *
     * @param   peer.Socket socket
     * @param   lang.XPException e
     */
    public function handleOutOfResources($socket, $reason);
  }
?>
