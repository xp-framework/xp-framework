<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'net.xp_framework.unittest.peer.sockets.AbstractSocketTest',
    'peer.Socket'
  );

  /**
   * TestCase
   *
   * @see      xp://peer.Socket
   */
  class SocketTest extends AbstractSocketTest {
    
    /**
     * Creates a new client socket
     *
     * @param   string addr
     * @param   int port
     * @return  peer.Socket
     */
    protected function newSocket($addr, $port) {
      return new Socket($addr, $port);
    }
  }
?>
