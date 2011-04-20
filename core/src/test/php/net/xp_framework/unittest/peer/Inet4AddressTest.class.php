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
   * TestCase
   *
   * @see      reference
   * @purpose  purpose
   */
  class Inet4AddressTest extends TestCase {
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      // TODO: Fill code that gets executed before every test method
      //       or remove this method
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function createAddress() {
      $this->assertEquals('127.0.0.1', create(new Inet4Address('127.0.0.1'))->getAddress());
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function loopbackAddress() {
      $this->assertTrue(create(new Inet4Address('127.0.0.1'))->isLoopback());
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function alternativeLoopbackAddress() {
      $this->assertTrue(create(new Inet4Address('127.0.0.200'))->isLoopback());
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function inSubnet() {
      $this->assertTrue(create(new Inet4Address('192.168.2.1'))->inSubnet('192.168.2/24'));
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function notInSubnet() {
      $this->assertFalse(create(new Inet4Address('192.168.2.1'))->inSubnet('172.17.0.0/12'));
    }
    
    
    /**
     * Test
     *
     */
    #[@test]
    public function hostInOwnHostSubnet() {
      $this->assertTrue(create(new Inet4Address('172.17.29.6'))->inSubnet('172.17.29.6/32'));
    }
    
    /**
     * Test
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function illegalSubnet() {
      create(new Inet4Address('172.17.29.6'))->inSubnet('172.17.29.6/33');
    }
    
    
    
    
    
  }
?>
