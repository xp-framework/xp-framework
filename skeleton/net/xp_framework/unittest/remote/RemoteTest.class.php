<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'util.profiling.unittest.TestCase',
    'net.xp_framework.unittest.remote.MockProtocolHandler',
    'remote.Remote',
    'remote.HandlerFactory'
  );

  /**
   * Unit test for Remote (entry-point) class
   *
   * @see      xp://remote.Remote
   * @purpose  TestCase
   */
  class RemoteTest extends TestCase {

    /**
     * Static initializer. Registers the protocol "mock" with the
     * MockProtocolHandler class.
     *
     * @model   static
     * @access  public
     */
    function __static() {
      HandlerFactory::protocol('mock', XPClass::forName('net.xp_framework.unittest.remote.MockProtocolHandler'));
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
     * Test forName() returns a Remote instance.
     *
     * @access  public
     */
    #[@test]
    function forNameSucceeds() {
      $r= &Remote::forName('mock://no.host.needed');
      $this->assertClass($r, 'remote.Remote');
    }

    /**
     * Test forName() method throws an IOException in case connecting
     * to the remote side fails
     *
     * @access  public
     */
    #[@test, @expect('io.IOException')]
    function forNameFailsToConnect() {
      Remote::forName('mock://no.host.needed?failto=connect');
    }
  }
?>
