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
   * @ext      sockets
   * @see      xp://peer.BSDSocket
   */
  class BSDSocketTest extends AbstractSocketTest {

    /**
     * Setup this test case
     *
     */
    public function setUp() {
      if (!Runtime::getInstance()->extensionAvailable('sockets')) {
        throw new PrerequisitesNotMetError('Sockets extension not available', NULL, array('ext/sockets'));
      }
      parent::setUp();
    }
    
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
