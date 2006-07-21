<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'org.dict.DictClient', 
    'util.profiling.unittest.TestCase'
  );

  /**
   * Test DICT client
   *
   * @see      xp://org.dict.DictClient
   * @purpose  Unit Test
   */
  class DictTest extends TestCase {
    public
      $dc      = NULL,
      $server  = '',
      $port    = 0;
      
    /**
     * Constructor
     *
     * @access  public
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
     * @access  public
     */
    public function setUp() {
      $this->dc= &new DictClient();
      try {
        $this->dc->connect($this->server, (int)$this->port);
      } catch (Exception $e) {
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
     * @access  public
     */
    public function tearDown() {
      $this->dc->close();
    }
    
    /**
     * Test getting a definition
     *
     * @access  public
     */
    #[@test]
    public function testDefinition() {
      $definition= $this->dc->getDefinition('XP', '*');
      return $definition;
    }
  }
?>
