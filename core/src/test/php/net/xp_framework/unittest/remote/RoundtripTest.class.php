<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
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
    public
      $endpoint = '',
      $instance = NULL;

    /**
     * Constructor
     *
     * @param   string name
     * @param   string endpoint
     */
    public function __construct($name, $endpoint) {
      parent::__construct($name);
      $this->endpoint= $endpoint;
    }
    
    /**
     * Setup method
     *
     * @throws  unittest.PrerequisitesNotMetError
     */
    public function setUp() {
      try {
        $remote= Remote::forName('xp://'.$this->endpoint.'/');
        $remote && $home= $remote->lookup('xp/demo/Roundtrip');
        $home && $this->instance= $home->create();
      } catch (XPException $e) {
        throw new PrerequisitesNotMetError(
          'Make sure xp/demo/Roundtrip is deployed @ '.$this->endpoint, 
          $e
        );
      }
    }

    /**
     * Helper method
     *
     * @param   string method
     * @param   mixed value
     * @throws  unittest.AssertionFailedError
     */
    protected function assertRoundtrip($method, $value) {
      $this->assertEquals($value, $this->instance->$method($value));
    }
    
    /**
     * Test echoString() method
     *
     */
    #[@test]
    public function echoString() {
      $this->assertRoundtrip(__FUNCTION__, 'He was called the "Übercoder", said Tom\'s friend');
    }

    /**
     * Test echoInt() method
     *
     */
    #[@test]
    public function echoInt() {
      foreach (array(1, 0, -1) as $val) {
        $this->assertRoundtrip(__FUNCTION__, $val);
      }
    }

    /**
     * Test echoDouble() method
     *
     */
    #[@test]
    public function echoDouble() {
      foreach (array(1.5, 0.0, -6.1) as $val) {
        $this->assertRoundtrip(__FUNCTION__, $val);
      }
    }

    /**
     * Test echoBool() method
     *
     */
    #[@test]
    public function echoBool() {
      $this->assertRoundtrip(__FUNCTION__, TRUE);
      $this->assertRoundtrip(__FUNCTION__, FALSE);
    }

    /**
     * Test echoNull() method
     *
     */
    #[@test]
    public function echoNull() {
      $this->assertRoundtrip(__FUNCTION__, NULL);
    }

    /**
     * Test echoDate() method
     *
     */
    #[@test]
    public function echoDate() {
      $this->assertRoundtrip(__FUNCTION__, Date::now());
    }

    /**
     * Test echoInt() method
     *
     */
    #[@test]
    public function echoHash() {
      $this->assertRoundtrip(__FUNCTION__, array(
        'localpart'   => 'xp',
        'domain'      => 'php3.de'
      ));
    }

    /**
     * Test echoArray() method
     *
     */
    #[@test]
    public function echoArray() {
      $this->assertRoundtrip(__FUNCTION__, new ArrayList(array(1, 2, 3)));
    }

    /**
     * Test passing a string to the echoArray() method, this should throw
     * a RemoteException.
     *
     */
    #[@test, @expect('remote.RemoteException')]
    public function incorrectArgumentsToArrayMethod() {
      $this->instance->echoArray('A STRING, MAN!');
    }
  }
?>
