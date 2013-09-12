<?php namespace net\xp_framework\unittest\peer\net;

use unittest\TestCase;
use peer\net\NetworkParser;


/**
 * Test network parser
 *
 * @purpose Testcase
 */
class NetworkParserTest extends TestCase {
  protected
    $cut  = null;

  /**
   * Set up test fixture
   *
   */
  public function setUp() {
    $this->cut= new NetworkParser();
  }

  /**
   * Test parsing ipv4 network string
   * 
   */
  #[@test]
  public function parseV4Network() {
    $this->assertEquals(
      new \peer\net\Network(new \peer\net\Inet4Address('192.168.1.1'), 24),
      $this->cut->parse('192.168.1.1/24')
    );
  }

  /**
   * Test parsing illegal network fails
   *
   */
  #[@test, @expect('lang.FormatException')]
  public function parseV4NetworkThrowsExceptionOnIllegalNetworkString() {
    $this->cut->parse('192.168.1.1 b24');
  }

  /**
   * Test parsing ipv6 network string
   */
  #[@test]
  public function parseV6Network() {
    $this->assertEquals(
      new \peer\net\Network(new \peer\net\Inet6Address('fc00::'), 7),
      $this->cut->parse('fc00::/7')
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function tryParse() {
    $this->assertEquals(
      new \peer\net\Network(new \peer\net\Inet4Address('172.16.0.0'), 12),
      $this->cut->tryParse('172.16.0.0/12')
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function tryParseReturnsNullOnFailure() {
    $this->assertEquals(null, $this->cut->tryParse('not a network'));
  }
}
