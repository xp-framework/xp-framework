<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'peer.Inet4Address'
  );

  /**
   * Test for Inet4Address
   *
   * @see      xp://peer.Inet4Address
   * @purpose  TestCase
   */
  class Inet4AddressTest extends TestCase {
  
    /**
     * Test creation of address
     *
     */
    #[@test]
    public function createAddress() {
      $this->assertEquals('127.0.0.1', create(new Inet4Address('127.0.0.1'))->getAddress());
    }

    /**
     * Test creation of IP from some string raises exception
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function createInvalidAddressRaisesException() {
      new Inet4Address('Who am I');
    }

    /**
     * Test creation of invalid formatted IP raises exception
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function createInvalidAddressThatLooksLikeAddressRaisesException() {
      new Inet4Address('10.0.0.355');
    }
    
    /**
     * Test creation of ip from string with too many dot blocks
     * raises an exception
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function createInvalidAddressWithTooManyBlocksRaisesException() {
      new Inet4Address('10.0.0.255.5');
    }

    /**
     * 127.0.0.1 should be detected as loopback
     *
     */
    #[@test]
    public function loopbackAddress() {
      $this->assertTrue(create(new Inet4Address('127.0.0.1'))->isLoopback());
    }
    
    /**
     * 127.0.0.x should be detected as loopback
     *
     */
    #[@test]
    public function alternativeLoopbackAddress() {
      $this->assertTrue(create(new Inet4Address('127.0.0.200'))->isLoopback());
    }
    
    /**
     * Test subnet determination works
     *
     */
    #[@test]
    public function inSubnet() {
      $this->assertTrue(create(new Inet4Address('192.168.2.1'))->inSubnet('192.168.2/24'));
    }
    
    /**
     * Test ip is not part of subnet
     *
     */
    #[@test]
    public function notInSubnet() {
      $this->assertFalse(create(new Inet4Address('192.168.2.1'))->inSubnet('172.17.0.0/12'));
    }
    
    /**
     * Test that a host is in his own host-subnet
     *
     */
    #[@test]
    public function hostInOwnHostSubnet() {
      $this->assertTrue(create(new Inet4Address('172.17.29.6'))->inSubnet('172.17.29.6/32'));
    }
    
    /**
     * Test invalid formatted net raises exception
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function illegalSubnet() {
      create(new Inet4Address('172.17.29.6'))->inSubnet('172.17.29.6/33');
    }
  }
?>
