<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'peer.net.NameserverLookup'
  );

  /**
   * Test nameserver lookup API
   *
   * @see   xp://peer.net.NameserverLookup'
   */
  class NameserverLookupTest extends TestCase {
    protected $cut= NULL;

    /**
     * Sets up test case and defines dummy nameserver lookup fixture
     */
    public function setUp() {
      $this->cut= newinstance('peer.net.NameserverLookup', array(), '{
        protected $results= array();

        public function addLookup($ip, $type= "ip") {
          $this->results[]= array($type => $ip);
        }

        protected function _nativeLookup($what, $type) {
          return $this->results;
        }
      }');
    }

    /**
     * Test lookup localhost
     *
     */
    #[@test]
    public function lookupLocalhostAllInet4() {
      $this->cut->addLookup('127.0.0.1');
      $this->assertEquals(array(new Inet4Address('127.0.0.1')), $this->cut->lookupAllInet4('localhost'));
    }

    /**
     * Test lookup localhost
     *
     */
    #[@test]
    public function lookupLocalhostInet4() {
      $this->cut->addLookup('127.0.0.1');
      $this->assertEquals(new Inet4Address('127.0.0.1'), $this->cut->lookupInet4('localhost'));
    }

    /**
     * Test lookup localhost
     *
     */
    #[@test]
    public function lookupLocalhostAllInet6() {
      $this->cut->addLookup('::1', 'ipv6');
      $this->assertEquals(array(new Inet6Address('::1')), $this->cut->lookupAllInet6('localhost'));
    }

    /**
     * Test lookup localhost
     *
     */
    #[@test]
    public function lookupLocalhostInet6() {
      $this->cut->addLookup('::1', 'ipv6');
      $this->assertEquals(new Inet6Address('::1'), $this->cut->lookupInet6('localhost'));
    }

    /**
     * Test lookup localhost
     *
     */
    #[@test]
    public function lookupLocalhostAll() {
      $this->cut->addLookup('127.0.0.1');
      $this->cut->addLookup('::1', 'ipv6');
      
      $this->assertEquals(
        array(new Inet4Address('127.0.0.1'), new Inet6Address('::1')),
        $this->cut->lookupAll('localhost')
      );
    }

    /**
     * Test lookup localhost
     *
     */
    #[@test]
    public function lookupLocalhost() {
      $this->cut->addLookup('127.0.0.1');
      $this->cut->addLookup('::1', 'ipv6');

      $this->assertEquals(
        new Inet4Address('127.0.0.1'),
        $this->cut->lookup('localhost')
      );
    }

    /**
     * Test nonexistant lookup returns empty array
     *
     */
    #[@test]
    public function lookupAllNonexistantGivesEmptyArray() {
      $this->assertEquals(array(), $this->cut->lookupAll('localhost'));
    }

    /**
     * Test nonexistant lookup returns empty array
     *
     */
    #[@test, @expect('lang.ElementNotFoundException')]
    public function lookupNonexistantThrowsException() {
      $this->cut->lookup('localhost');
    }

    /**
     * Test
     *
     */
    #[@test]
    public function reverseLookup() {
      $this->cut->addLookup('localhost', 'target');
      $this->assertEquals('localhost', $this->cut->reverseLookup(new Inet4Address('127.0.0.1')));
    }

    /**
     * Test
     *
     */
    #[@test, @expect('lang.ElementNotFoundException')]
    public function nonexistingReverseLookupCausesException() {
      $this->cut->reverseLookup(new Inet4Address('192.168.1.1'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function tryReverseLookupReturnsNullWhenNoneFound() {
      $this->assertNull($this->cut->tryReverseLookup(new Inet4Address('192.178.1.1')));
    }
  }
?>
