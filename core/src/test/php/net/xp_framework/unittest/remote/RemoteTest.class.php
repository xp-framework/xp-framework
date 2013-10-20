<?php namespace net\xp_framework\unittest\remote;

use unittest\TestCase;
use remote\Remote;
use remote\HandlerInstancePool;
use remote\HandlerFactory;


define('REMOTE_SPEC_ONE',   'mock://remote.host1');
define('REMOTE_SPEC_TWO',   'mock://remote.host2');
define('REMOTE_SPEC_THREE', 'mock://remote.host3');
define('REMOTE_SPEC_OTHER', 'mock://other.host');

/**
 * Unit test for Remote (entry-point) class
 *
 * @see      xp://remote.Remote
 * @purpose  TestCase
 */
class RemoteTest extends TestCase {
  public
    $handler= array();

  static function __static() {
    HandlerFactory::getInstance()->register(
      'mock', 
      \lang\XPClass::forName('net.xp_framework.unittest.remote.MockProtocolHandler')
    );
  }
  
  /**
   * Setup method
   *
   */
  public function setUp() {
    $pool= HandlerInstancePool::getInstance();
    
    foreach (array(
      REMOTE_SPEC_ONE     => true,    // Cluster machine #1
      REMOTE_SPEC_TWO     => false,   // Cluster machine #2
      REMOTE_SPEC_THREE   => false,   // Cluster machine #3
      REMOTE_SPEC_OTHER   => true     // Other machine
    ) as $spec => $avail) {
      $this->handler[$spec]= $pool->acquire($spec);
      $this->handler[$spec]->server['available']= $avail;
    }
  }
  
  /**
   * Test handler member is an array of MockProtocolHandlers
   *
   */
  #[@test]
  public function mockHandler() {
    foreach ($this->handler as $handler) {
      $this->assertClass($handler, 'net.xp_framework.unittest.remote.MockProtocolHandler');
    }
  }
  
  /**
   * Test forName() returns a Remote instance.
   *
   */
  #[@test]
  public function forNameSucceeds() {
    Remote::forName(REMOTE_SPEC_ONE);
  }

  /**
   * Test forName() method throws a RemoteException in case connecting
   * to the remote side fails
   *
   */
  #[@test, @expect('remote.RemoteException')]
  public function forNameFailsToConnect() {
    Remote::forName(REMOTE_SPEC_TWO);
  }

  /**
   * Test forName() method succeeds for a cluster with one machine 
   * down and one running (either way around)
   *
   */
  #[@test]
  public function forNameSucceedsForCluster() {
    Remote::forName(REMOTE_SPEC_TWO.','.REMOTE_SPEC_ONE);
    Remote::forName(REMOTE_SPEC_ONE.','.REMOTE_SPEC_TWO);
  }

  /**
   * Test forName() method succeeds for a cluster with all machines
   * down.
   *
   */
  #[@test, @expect('remote.RemoteException')]
  public function forNameFailsToConnectCluster() {
    Remote::forName(REMOTE_SPEC_TWO.','.REMOTE_SPEC_THREE);
    Remote::forName(REMOTE_SPEC_THREE.','.REMOTE_SPEC_TWO);
  }

  /**
   * Test forName() returns the same Remote instance when invoked
   * twice with the same DSN.
   *
   */
  #[@test]
  public function forNameSameInstance() {
    $this->assertTrue(Remote::forName(REMOTE_SPEC_ONE) === Remote::forName(REMOTE_SPEC_ONE), 'a != a');
    $this->assertTrue(Remote::forName(REMOTE_SPEC_ONE) !== Remote::forName(REMOTE_SPEC_OTHER), 'a == b');
  }

  /**
   * Test forName() method throws a RemoteException in case the
   * protocol is unknown.
   *
   */
  #[@test, @expect('remote.RemoteException')]
  public function forNameFailsForUnknownProtocol() {
    Remote::forName('unknown://irrelevant');
  }

  /**
   * Test forName() with and without parameters in DSN string to return
   * always the same handler object (required to make transactions work)
   *
   */
  #[@test]
  public function forNameEqualsWithDifferentQueryString() {
    $remote1= Remote::forName(REMOTE_SPEC_ONE);
    
    // HACK: Reset initialization status to FALSE otherwise it will be
    // initialized again and we get "Already initialized" exception
    $this->handler[REMOTE_SPEC_ONE]->server['initialized']= false;
    
    $remote2= Remote::forName(REMOTE_SPEC_ONE.'?log=default');
    $this->assertEquals($remote1->_handler, $remote2->_handler);
  }
 
  /**
   * Test lookup() method
   *
   */
  #[@test]
  public function lookup() {
    $r= Remote::forName(REMOTE_SPEC_ONE);
    
    // Bind a person object
    $person= new Person();
    $this->handler[REMOTE_SPEC_ONE]->server['ctx']['xp/demo/Person']= $person;

    // Lookup the person object
    $this->assertEquals($person, $r->lookup('xp/demo/Person'));
  }

  /**
   * Test lookup() method
   *
   */
  #[@test, @expect('remote.NameNotFoundException')]
  public function lookupNonExistantName() {
    Remote::forName(REMOTE_SPEC_ONE)->lookup('does/not/Exist');
  }
}
