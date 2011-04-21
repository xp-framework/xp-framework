<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'peer.NetworkParser'
  );

  /**
   * Test network parser
   *
   * @purpose Testcase
   */
  class NetworkParserTest extends TestCase {
    protected
      $cut  = NULL;

    /**
     * Test parsing ipv4 network string
     * 
     */
    #[@test]
    public function parseV4Network() {
      $parser= new NetworkParser();

      $this->assertEquals(
        new Network(new Inet4Address('192.168.1.1'), 24),
        $parser->parse('192.168.1.1/24')
      );
    }

    /**
     * Test parsing illegal network fails
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function parseV4NetworkThrowsExceptionOnIllegalNetworkString() {
      $parser= new NetworkParser();
      $parser->parse('192.168.1.1 b24');
    }

    /**
     * Test parsing ipv6 network string
     */
    #[@test]
    public function parseV6Network() {
      $parser= new NetworkParser();

      $this->assertEquals(
        new Network(new Inet6Address('fc00::'), 7),
        $parser->parse('fc00::/7')
      );
    }

  }
?>