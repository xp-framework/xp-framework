<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'util.profiling.unittest.TestCase',
    'net.xp_framework.unittest.remote.MockProtocolHandler',
    'net.xp_framework.unittest.remote.Person',
    'remote.Remote',
    'remote.HandlerInstancePool',
    'remote.HandlerFactory'
  );

  define('REMOTE_SPEC_ONE',   'mock://remote.host1');
  define('REMOTE_SPEC_TWO',   'mock://remote.host2');
  define('REMOTE_SPEC_OTHER', 'mock://other.host');

  /**
   * Unit test for Remote (entry-point) class
   *
   * @see      xp://remote.Remote
   * @purpose  TestCase
   */
  class RemoteTest extends TestCase {
    var
      $handler= NULL;

    /**
     * Static initializer. Registers the protocol "mock" with the
     * MockProtocolHandler class.
     *
     * @model   static
     * @access  public
     */
    function __static() {
      $hf= &HandlerFactory::getInstance();
      $hf->register('mock', XPClass::forName('net.xp_framework.unittest.remote.MockProtocolHandler'));
    }
    
    /**
     * Setup method
     *
     * @access  public
     */
    function setUp() {
      $pool= &HandlerInstancePool::getInstance();
      $this->handler= &$pool->acquire(new URL(REMOTE_SPEC_ONE));
      $pool->pool(new URL(REMOTE_SPEC_TWO), $this->handler);
      $this->handler->server['hosts']= array('remote.host1', 'remote.host2');
      $other= &$pool->acquire(new URL(REMOTE_SPEC_OTHER));
      $other->server['hosts']= array('other.host');
    }
    
    /**
     * Test handler member is a MockProtocolHandler
     *
     * @access  public
     */
    #[@test]
    function mockHandler() {
      $this->assertClass($this->handler, 'net.xp_framework.unittest.remote.MockProtocolHandler');
    }
    
    /**
     * Test forName() returns a Remote instance.
     *
     * @access  public
     */
    #[@test]
    function forNameSucceeds() {
      Remote::forName(REMOTE_SPEC_ONE);
    }

    /**
     * Test forName() returns the same Remote instance when invoked
     * twice with the same DSN.
     *
     * @access  public
     */
    #[@test]
    function forNameSameInstance() {
      $this->assertTrue(Remote::forName(REMOTE_SPEC_ONE) === Remote::forName(REMOTE_SPEC_ONE), 'a != a');
      $this->assertTrue(Remote::forName(REMOTE_SPEC_ONE) !== Remote::forName(REMOTE_SPEC_OTHER), 'a == b');
    }

    /**
     * Test forName() method throws a RemoteException in case the
     * protocol is unknown.
     *
     * @access  public
     */
    #[@test, @expect('remote.RemoteException')]
    function forNameFailsForUnknownProtocol() {
      Remote::forName('unknown://irrelevant');
    }

    /**
     * Test forName() method throws a RemoteException in case connecting
     * to the remote side fails
     *
     * @access  public
     */
    #[@test, @expect('remote.RemoteException')]
    function forNameFailsToConnect() {
      Remote::forName('mock://unknown.host');
    }

    /**
     * Test lookup() method
     *
     * @access  public
     */
    #[@test]
    function lookup() {
      $r= &Remote::forName(REMOTE_SPEC_ONE);
      
      // Bind a person object
      $person= &new Person();
      $this->handler->server['ctx']['xp/demo/Person']= &$person;

      // Lookup the person object
      $lookup= &$r->lookup('xp/demo/Person');
      $this->assertEquals($person, $lookup);
    }

    /**
     * Test lookup() method
     *
     * @access  public
     */
    #[@test, @expect('remote.NameNotFoundException')]
    function lookupNonExistantName() {
      $r= &Remote::forName(REMOTE_SPEC_ONE);
      $r->lookup('does/not/Exist');
    }
  }
?>
