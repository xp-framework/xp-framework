<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'net.xp_framework.unittest.peer.server.AbstractServerTest',
    'net.xp_framework.unittest.peer.server.AcceptTestingProtocol'
  );

  /**
   * TestCase for server protocol with accept handler
   *
   */
  class AcceptingServerTest extends AbstractServerTest {
    
    /**
     * Starts server in background
     *
     */
    #[@beforeClass]
    public static function startServer() {
      parent::startServerWith('net.xp_framework.unittest.peer.server.AcceptTestingProtocol');
    }

    /**
     * Test handleConnect() is invoked
     *
     */
    #[@test]
    public function connected() {
      $this->connect();
      $this->assertHandled(array('ACCEPT', 'CONNECT'));
    }

    /**
     * Test handleDisconnect() is invoked
     *
     */
    #[@test]
    public function disconnected() {
      $this->connect();
      $this->conn->close();
      $this->assertHandled(array('ACCEPT', 'CONNECT', 'DISCONNECT'));
    }

    /**
     * Test handleError() is invoked
     *
     */
    #[@test, @ignore('Fragile test, dependant on OS / platform and implementation vagaries')]
    public function error() {
      $this->connect();
      $this->conn->write("SEND\n");
      $this->conn->close();
      $this->assertHandled(array('ACCEPT', 'CONNECT', 'ERROR'));
    }
  }
?>
