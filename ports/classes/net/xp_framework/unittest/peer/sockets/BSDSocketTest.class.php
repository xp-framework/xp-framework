<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'net.xp_framework.unittest.peer.sockets.AbstractSocketTest',
    'peer.BSDSocket'
  );

  /**
   * TestCase
   *
   * @see      xp://peer.BSDSocket
   */
  class BSDSocketTest extends AbstractSocketTest {
    
    /**
     * Creates a new client socket
     *
     * @param   string addr
     * @param   int port
     * @return  peer.Socket
     */
    protected function newSocket($addr, $port) {
      return new BSDSocket($addr, $port);
    }
  }
?>
