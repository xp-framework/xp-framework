<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Server Protocol
   *
   * @see      xp://peer.server.Server#setProtocol
   * @purpose  Interface
   */
  interface ServerProtocol {
  
    /**
     * Initialize Protocol
     *
     * @return  bool
     */
    public function initialize();

    /**
     * Handle client connect
     *
     * @param   peer.Socket socket
     */
    public function handleConnect($socket);

    /**
     * Handle client disconnect
     *
     * @param   peer.Socket socket
     */
    public function handleDisconnect($socket);
  
    /**
     * Handle client data
     *
     * @param   peer.Socket socket
     * @return  mixed
     */
    public function handleData($socket);

    /**
     * Handle I/O error
     *
     * @param   peer.Socket socket
     * @param   lang.XPException e
     */
    public function handleError($socket, $e);
  
  }
?>
