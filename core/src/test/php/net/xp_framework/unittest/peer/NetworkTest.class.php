<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'peer.Network'
  );

  /**
   * Test for Network
   *
   * @see      xp://peer.Network
   * @purpose  Testcase
   */
  class NetworkTest extends TestCase {

    /**
     * Create network
     * 
     */
    #[@test]
    public function createNetwork() {
      $net= new Network(new Inet4Address("127.0.0.1"), 24);

      $this->assertEquals('127.0.0.1/24', $net->getAddress());
    }
  }
?>