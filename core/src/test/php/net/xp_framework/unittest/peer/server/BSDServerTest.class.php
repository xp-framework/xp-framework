<?php namespace net\xp_framework\unittest\peer\server;

/**
 * TestCase for default server protocol
 *
 * @see  xp://peer.server.Server
 */
#[@action(new \unittest\actions\ExtensionAvailable('sockets'))]
class BSDServerTest extends AbstractServerTest {
  
  /**
   * Starts server in background
   */
  #[@beforeClass]
  public static function startServer() {
    parent::startServerWith('net.xp_framework.unittest.peer.server.TestingProtocol', 'BSDSocketImpl');
  }

  #[@test]
  public function connected() {
    $this->connect();
    $this->assertHandled(array('CONNECT'));
  }

  #[@test]
  public function disconnected() {
    $this->connect();
    $this->conn->close();
    $this->assertHandled(array('CONNECT', 'DISCONNECT'));
  }

  #[@test, @ignore('Fragile test, dependant on OS / platform and implementation vagaries')]
  public function error() {
    $this->connect();
    $this->conn->write("SEND\n");
    $this->conn->close();
    $this->assertHandled(array('CONNECT', 'ERROR'));
  }
}
