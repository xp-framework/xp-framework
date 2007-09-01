<?php
/* This class is part of the XP framework
 *
 * $Id: DictTest.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace net::xp_framework::unittest::peer;
 
  ::uses(
    'org.dict.DictClient',
    'unittest.TestCase'
  );

  /**
   * Test DICT client
   *
   * @see      xp://org.dict.DictClient
   * @purpose  Unit Test
   */
  class DictTest extends unittest::TestCase {
    public
      $dc      = NULL,
      $server  = '',
      $port    = 0;
      
    /**
     * Constructor
     *
     * @param   string name
     * @param   string uri
     */
    public function __construct($name, $server, $port) {
      $this->server= $server;
      $this->port= $port;
      parent::__construct($name);
    }
      
    /**
     * Setup function
     *
     */
    public function setUp() {
      $this->dc= new org::dict::DictClient();
      try {
        $this->dc->connect($this->server, (int)$this->port);
      } catch (::Exception $e) {
        throw (new PrerequisitesNotMetError(
          PREREQUISITE_INITFAILED,
          $e,
          array('connect', $this->server, $this->port)
        ));
      }
    }
    
    /**
     * Tear down this test case.
     *
     */
    public function tearDown() {
      $this->dc->close();
    }
    
    /**
     * Test getting a definition
     *
     */
    #[@test]
    public function testDefinition() {
      $definition= $this->dc->getDefinition('XP', '*');
      return $definition;
    }
  }
?>
