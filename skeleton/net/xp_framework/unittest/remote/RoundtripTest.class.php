<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'util.profiling.unittest.TestCase',
    'net.xp_framework.beans.stateless.Roundtrip',
    'net.xp_framework.beans.stateless.RoundtripHome',
    'remote.Remote'
  );

  /**
   * Unit test for xp/demo/Roundtrip bean class
   *
   * @see      xp://net.xp_framework.beans.stateless.Roundtrip
   * @purpose  TestCase
   */
  class RoundtripTest extends TestCase {
    var
      $endpoint = '',
      $instance = NULL;

    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     * @param   string endpoint
     */
    function __construct($name, $endpoint) {
      parent::__construct($name);
      $this->endpoint= $endpoint;
    }
    
    /**
     * Setup method
     *
     * @access  public
     * @throws  util.profiling.unittest.PrerequisitesNotMetError
     */
    function setUp() {
      try(); {
        $remote= &Remote::forName('xp://'.$this->endpoint.'/');
        $remote && $home= &$remote->lookup('xp/demo/Roundtrip');
        $home && $this->instance= &$home->create();
      } if (catch('Exception', $e)) {
        return throw(new PrerequisitesNotMetError(
          'Make sure xp/demo/Roundtrip is deployed @ '.$this->endpoint, 
          $e
        ));
      }
    }

    /**
     * Helper method
     *
     * @access  protected
     * @param   string method
     * @param   mixed value
     * @throws  util.profiling.unittest.AssertionFailedError
     */
    function assertRoundtrip($method, $value) {
      $this->assertEquals($value, $this->instance->$method($value));
    }
    
    /**
     * Test echoString() method
     *
     * @access  public
     */
    #[@test]
    function echoString() {
      $this->assertRoundtrip(__FUNCTION__, 'He was called the "Übercoder", said Tom\'s friend');
    }

    /**
     * Test echoInt() method
     *
     * @access  public
     */
    #[@test]
    function echoInt() {
      foreach (array(1, 0, -1) as $val) {
        $this->assertRoundtrip(__FUNCTION__, $val);
      }
    }

    /**
     * Test echoDouble() method
     *
     * @access  public
     */
    #[@test]
    function echoDouble() {
      foreach (array(1.5, 0.0, -6.1) as $val) {
        $this->assertRoundtrip(__FUNCTION__, $val);
      }
    }

    /**
     * Test echoBool() method
     *
     * @access  public
     */
    #[@test]
    function echoBool() {
      $this->assertRoundtrip(__FUNCTION__, TRUE);
      $this->assertRoundtrip(__FUNCTION__, FALSE);
    }

    /**
     * Test echoNull() method
     *
     * @access  public
     */
    #[@test]
    function echoNull() {
      $this->assertRoundtrip(__FUNCTION__, NULL);
    }

    /**
     * Test echoDate() method
     *
     * @access  public
     */
    #[@test]
    function echoDate() {
      $this->assertRoundtrip(__FUNCTION__, Date::now());
    }

    /**
     * Test echoInt() method
     *
     * @access  public
     */
    #[@test]
    function echoHash() {
      $this->assertRoundtrip(__FUNCTION__, array(
        'localpart'   => 'xp',
        'domain'      => 'php3.de'
      ));
    }

    /**
     * Test echoArray() method
     *
     * @access  public
     */
    #[@test]
    function echoArray() {
      $this->assertRoundtrip(__FUNCTION__, new ArrayList(array(1, 2, 3)));
    }

    /**
     * Test passing a string to the echoArray() method, this should throw
     * a RemoteException.
     *
     * @access  public
     */
    #[@test, @expect('remote.RemoteException')]
    function incorrectArgumentsToArrayMethod() {
      $this->instance->echoArray('A STRING, MAN!');
    }
  }
?>
