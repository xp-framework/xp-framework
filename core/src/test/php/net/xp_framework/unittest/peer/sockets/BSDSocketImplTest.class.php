<?php namespace net\xp_framework\unittest\peer\sockets;

use unittest\actions\ExtensionAvailable;
use unittest\actions\Actions;
use net\xp_framework\unittest\StartServer;
use peer\Socket;
use peer\SocketImpl;

/**
 * TestCase for BSD-Sockets based socket implementation
 *
 * @ext   sockets
 * @see   xp://peer.Socket
 */
#[@action([
#  new ExtensionAvailable('sockets'),
#  new StartServer('net.xp_framework.unittest.peer.sockets.TestingServer', 'connected', 'shutdown')
#])]
class BSDSocketImplTest extends AbstractSocketTest {

  /**
   * Creates a new client socket
   *
   * @param   string addr
   * @param   int port
   * @return  peer.Socket
   */
  protected function newSocket($addr, $port) {
    return new Socket($addr, $port, NULL, SocketImpl::$BSD);
  }
}