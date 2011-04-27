<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'peer.net.Inet6Address',
    'peer.net.Network'
  );

  /**
   * Test for Inet6Address
   *
   * @see      xp://peer.Inet6Address
   * @purpose  Testcase
   */
  class Inet6AddressTest extends TestCase {
  
    /**
     * Test creation of address
     *
     */
    #[@test]
    public function createAddress() {
      $this->assertEquals(
        'febc:a574:382b:23c1:aa49:4592:4efe:9982',
        create(new Inet6Address('febc:a574:382b:23c1:aa49:4592:4efe:9982'))->asString()
      );
    }
    
    /**
     * Test getAddress() shortens address
     *
     */
    #[@test]
    public function addressIsShortened() {
      $this->assertEquals(
        'febc:a574:382b::4592:4efe:9982',
        create(new Inet6Address('febc:a574:382b:0000:0000:4592:4efe:9982'))->asString()
      );
    }
    
    /**
     * Test shortening only takes place once
     *
     */
    #[@test]
    public function addressShorteningOnlyTakesPlaceOnce() {
      $this->assertEquals(
        'febc::23c1:aa49:0:0:9982',
        create(new Inet6Address('febc:0000:0000:23c1:aa49:0000:0000:9982'))->asString()
      );
    }
    
    
    /**
     * Test hexquads become shortened if first digits are zero
     *
     */
    #[@test]
    public function hexquadsAreShortenedWhenStartingWithZero() {
      $this->assertEquals(
        'febc:a574:2b:23c1:aa49:4592:4efe:9982',
        create(new Inet6Address('febc:a574:002b:23c1:aa49:4592:4efe:9982'))->asString()
      );
    }
    
    /**
     * Test prefix is shortened
     *
     */
    #[@test]
    public function addressPrefixIsShortened() {
      $this->assertEquals(
        '::382b:23c1:aa49:4592:4efe:9982',
        create(new Inet6Address('0000:0000:382b:23c1:aa49:4592:4efe:9982'))->asString()
      );
    }
    
    /**
     * Test postfix is shortened
     *
     */
    #[@test]
    public function addressPostfixIsShortened() {
      $this->assertEquals(
        'febc:a574:382b:23c1:aa49::',
        create(new Inet6Address('febc:a574:382b:23c1:aa49:0000:0000:0000'))->asString()
      );
    }
    
    
    /**
     * Test loopback address is formatted correctly
     *
     */
    #[@test]
    public function loopbackAddress() {
      $this->assertEquals('::1', create(new Inet6Address('::1'))->asString());
    }
    
    /**
     * Test loopback address is detected
     *
     */
    #[@test]
    public function isLoopbackAddress() {
      $this->assertTrue(create(new Inet6Address('::1'))->isLoopback());
    }
    
    /**
     * Test alternative loopback address is detected
     *
     */
    #[@test]
    public function isNotLoopbackAddress() {
      $this->assertFalse(create(new Inet6Address('::2'))->isLoopback());
    }
    
    /**
     * Test subnet detection for loopback
     *
     */
    #[@test]
    public function inSubnet() {
      $this->assertTrue(create(new Inet6Address('::1'))->inSubnet(new Network(new Inet6Address('::1'), 120)));
    }
    
    /**
     * Test smallest possible subnet contains loopback
     *
     */
    #[@test]
    public function inSmallestPossibleSubnet() {
      $this->assertTrue(create(new Inet6Address('::1'))->inSubnet(new Network(new Inet6Address('::0'), 127)));
    }
    
    /**
     * Test address not being detected in subnet when its not
     *
     */
    #[@test]
    public function notInSubnet() {
      $this->assertFalse(create(new Inet6Address('::1'))->inSubnet(new Network(new Inet6Address('::0101'), 120)));
    }

    /**
     * Test invalid address is caught
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function illegalAddress() {
      new Inet6Address('::ffffff:::::a');
    }

    /**
     * Test invalid address is caught
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function anotherIllegalAddress() {
      new Inet6Address('');
    }

    /**
     * The same IPs should be equal
     *
     */
    #[@test]
    public function sameIPsShouldBeEqual() {
      $this->assertEquals(new Inet6Address('::1'), new Inet6Address('::1'));
    }

    /**
     * Different IPs should not be equal
     *
     */
    #[@test]
    public function differentIPsShouldBeDifferent() {
      $this->assertNotEquals(new Inet6Address('::1'), new Inet6Address('::fe08'));
    }

    /**
     * Check casting to string works
     *
     */
    #[@test]
    public function castToString() {
      $this->assertEquals('[::1]', (string)new Inet6Address('::1'));
    }

    /**
     * Test
     *
     * @see   http://en.wikipedia.org/wiki/Reverse_DNS_lookup#IPv6_reverse_resolution
     */
    #[@test]
    public function reversedNotation() {
      $this->assertEquals(
        'b.a.9.8.7.6.5.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.8.b.d.0.1.0.0.2.ip6.arpa',
        create(new Inet6Address('2001:db8::567:89ab'))->reversedNotation()
      );
    }
  }
?>
