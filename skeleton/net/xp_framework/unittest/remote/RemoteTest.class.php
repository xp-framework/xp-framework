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

  define('REMOTE_SPEC', 'mock://remote.host');

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
      $this->handler= &$pool->acquire(new URL(REMOTE_SPEC));
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
      $this->assertClass(Remote::forName(REMOTE_SPEC), 'remote.Remote');
    }

    /**
     * Test forName() returns the same Remote instance when invoked
     * twice with the same DSN.
     *
     * @access  public
     */
    #[@test]
    function forNameSameInstance() {
      $this->assertTrue(Remote::forName('mock://a') === Remote::forName('mock://a'), 'a != a');
      $this->assertTrue(Remote::forName('mock://a') !== Remote::forName('mock://b'), 'a == b');
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
      Remote::forName('mock://no.host.needed?failto=connect');
    }

    /**
     * Test lookup() method
     *
     * @access  public
     */
    #[@test]
    function lookup() {
      $r= &Remote::forName(REMOTE_SPEC);
      
      // Bind a person object
      $person= &new Person();
      $this->handler->ctx['xp/demo/Person']= &$person;  // HACK

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
      $r= &Remote::forName(REMOTE_SPEC);
      $r->lookup('does/not/Exist');
    }
  }
?>
