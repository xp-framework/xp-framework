<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'peer.Inet6Address'
  );

  /**
   * TestCase
   *
   * @see      reference
   * @purpose  purpose
   */
  class Inet6AddressTest extends TestCase {
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      // TODO: Fill code that gets executed before every test method
      //       or remove this method
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function tearDown() {
      // TODO: Fill code that gets executed after every test method
      //       or remove this method
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function createAddress() {
      $this->assertEquals(
        'febc:a574:382b:23c1:aa49:4592:4efe:9982',
        create(new Inet6Address('febc:a574:382b:23c1:aa49:4592:4efe:9982'))->getAddress()
      );
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function addressIsShortened() {
      $this->assertEquals(
        'febc:a574:382b::4592:4efe:9982',
        create(new Inet6Address('febc:a574:382b:0000:0000:4592:4efe:9982'))->getAddress()
      );
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function addressShorteningOnlyTakesPlaceOnce() {
      $this->assertEquals(
        'febc::23c1:aa49:0:0:9982',
        create(new Inet6Address('febc:0000:0000:23c1:aa49:0000:0000:9982'))->getAddress()
      );
    }
    
    
    /**
     * Test
     *
     */
    #[@test]
    public function hexquadsAreShortenedWhenStartingWithZero() {
      $this->assertEquals(
        'febc:a574:2b:23c1:aa49:4592:4efe:9982',
        create(new Inet6Address('febc:a574:002b:23c1:aa49:4592:4efe:9982'))->getAddress()
      );
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function addressPrefixIsShortened() {
      $this->assertEquals(
        '::382b:23c1:aa49:4592:4efe:9982',
        create(new Inet6Address('0000:0000:382b:23c1:aa49:4592:4efe:9982'))->getAddress()
      );
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function addressPostfixIsShortened() {
      $this->assertEquals(
        'febc:a574:382b:23c1:aa49::',
        create(new Inet6Address('febc:a574:382b:23c1:aa49:0000:0000:0000'))->getAddress()
      );
    }
    
    
    /**
     * Test
     *
     */
    #[@test]
    public function loopbackAddress() {
      $this->assertEquals('::1', create(new Inet6Address('::1'))->getAddress());
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function isLoopbackAddress() {
      $this->assertTrue(create(new Inet6Address('::1'))->isLoopback());
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function isNotLoopbackAddress() {
      $this->assertFalse(create(new Inet6Address('::2'))->isLoopback());
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function inSubnet() {
      $this->assertTrue(create(new Inet6Address('::1'))->inSubnet('::1/120'));
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function inSmallestPossibleSubnet() {
      $this->assertTrue(create(new Inet6Address('::1'))->inSubnet('::0/127'));
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function notInSubnet() {
      $this->assertFalse(create(new Inet6Address('::1'))->inSubnet('::0101/120'));
    }
  }
?>
