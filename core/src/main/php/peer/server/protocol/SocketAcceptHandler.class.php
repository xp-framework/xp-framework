<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Server Protocol: Accept sockets handler
   *
   * @test  xp://net.xp_framework.unittest.peer.server.AcceptingServerTest
   */
  interface SocketAcceptHandler {

    /**
     * Handle accepted socket. Return FALSE to make server drop connection
     * immediately, TRUE to continue on to handleConnect().
     *
     * @param   peer.Socket socket
     * @return  bool
     */
    public function handleAccept($socket);
  }
?>
