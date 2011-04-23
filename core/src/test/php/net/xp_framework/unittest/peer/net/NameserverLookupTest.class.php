<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'peer.net.NameserverLookup'
  );

  class NameserverLookupTest extends TestCase {
    protected
      $cut  = NULL;

    public function setUp() {
      $this->cut= newinstance('peer.net.NameserverLookup', array(), '{
        protected $results= array();

        public function setLookup(array $ret) {
          $this->results= $ret;
        }

        public function addLookup($ip) {
          $this->results[]= array("ip" => $ip);
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
    public function lookupLocalhostAllInet6() {
      $this->cut->addLookup('::1');
      $this->assertEquals(array(new Inet6Address('::1')), $this->cut->lookupAllInet6('localhost'));
    }

    /**
     * Test lookup localhost
     *
     */
    #[@test]
    public function lookupLocalhostAll() {
      $this->cut->addLookup('127.0.0.1');
      $this->cut->addLookup('::1');
      
      $this->assertEquals(
        array(new Inet4Address('127.0.0.1'), new Inet6Address('::1')),
        $this->cut->lookupAll('localhost')
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

  }

?>
