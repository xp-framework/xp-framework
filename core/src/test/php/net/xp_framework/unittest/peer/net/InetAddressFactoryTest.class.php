<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'peer.net.InetAddressFactory'
  );

  /**
   * Test class
   *
   * 
   */
  class InetAddressFactoryTest extends TestCase {
    protected
      $cut  = NULL;

    /**
     * SetUp
     *
     */
    public function setUp() {
      $this->cut= new InetAddressFactory();
    }

    /**
     * Parse 127.0.0.1
     *
     */
    #[@test]
    public function createLocalhostV4() {
      $this->assertInstanceOf('peer.net.Inet4Address', $this->cut->parse('127.0.0.1'));
    }

    /**
     * Parse invalid address that matches a valid one
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function parseInvalidAddressThatLooksLikeV4() {
      $this->cut->parse('3.33.333.333');
    }

    /**
     * Parse invalid address that matches a valid one
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function parseInvalidAddressThatAlsoLooksLikeV4() {
      $this->cut->parse('10..3.3');
    }

    /**
     * Test
     *
     */
    #[@test]
    public function tryParse() {
      $this->assertEquals(new Inet4Address('172.17.29.6'), $this->cut->tryParse('172.17.29.6'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function tryParseReturnsNullOnFailure() {
      $this->assertEquals(NULL, $this->cut->tryParse('not an ip address'));
    }

    /**
     * Parse localhost address
     * 
     */
    #[@test]
    public function parseLocalhostV6() {
      $this->assertInstanceOf('peer.net.Inet6Address', $this->cut->parse('::1'));
    }

    /**
     * Parse address
     *
     */
    #[@test]
    public function parseV6() {
      $this->assertInstanceOf('peer.net.Inet6Address', $this->cut->parse('fe80::a6ba:dbff:fefe:7755'));
    }

    /**
     * Parse address
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function parseThatLooksLikeV6() {
      $this->cut->parse('::ffffff:::::a');
    }
  }
?>