<?php namespace net\xp_framework\unittest\peer\sockets;

use net\xp_framework\unittest\StartServer;
use peer\Socket;
use peer\SocketImpl;

/**
 * TestCase for stream-based socket implementation
 *
 * @see      xp://peer.Socket
 */
#[@action(new StartServer('net.xp_framework.unittest.peer.sockets.TestingServer', 'connected', 'shutdown'))]
class StreamSocketImplTest extends AbstractSocketTest {
  
  /**
   * Creates a new client socket
   *
   * @param   string addr
   * @param   int port
   * @return  peer.Socket
   */
  protected function newSocket($addr, $port) {
    return new Socket($addr, $port, NULL, SocketImpl::$STREAM);
  }
}
