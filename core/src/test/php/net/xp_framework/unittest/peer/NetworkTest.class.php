<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'peer.Network',
    'peer.Inet4Address'
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
      $this->assertEquals('127.0.0.1/24', $net->getAddressAsString());
    }

    /**
     * Create network fails
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function createNetworkFailsIfTooLargeNetmaskGiven() {
      new Network(new Inet4Address("127.0.0.1"), 33);
    }

    /**
     * Create network fails
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function createNetworkFailsIfTooSmallNetmaskGiven() {
      new Network(new Inet4Address("127.0.0.1"), -1);
    }

    /**
     * Create network fails
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function createNetworkFailsIfNonIntegerNetmaskGiven() {
      new Network(new Inet4Address("127.0.0.1"), 0.5);
    }

    /**
     * Create network fails
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function createNetworkFailsIfStringGiven() {
      new Network(new Inet4Address("127.0.0.1"), "Hello");
    }

    /**
     * Retrieve network ip
     *
     */
    #[@test]
    public function networkAddress() {
      $net= new Network(new Inet4Address("127.0.0.0"), 24);
      $this->assertEquals(new Inet4Address("127.0.0.0"), $net->getNetworkAddress());
    }

    /**
     * Check if given IP is part of net
     *
     */
    #[@test]
    public function loopbackNetworkContainsLoopbackAddressV4() {
      $this->assertTrue(create(new Network(new Inet4Address('127.0.0.5'), 24))->contains(new Inet4Address('127.0.0.1')));
    }

    #[@test]
    public function equalNetworksAreEqual() {
      $this->assertEquals(
        new Network(new Inet4Address('127.0.0.1'), 8),
        new Network(new Inet4Address('127.0.0.1'), 8)
      );
    }
  }
?>