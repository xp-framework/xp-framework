<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'rdbms.DriverManager', 
    'util.profiling.unittest.TestCase'
  );

  /**
   * Test rdbms API
   *
   * @purpose  Unit Test
   */
  class DBTest extends TestCase {
    var
      $conn = NULL,
      $dsn  = '';
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     * @param   string dsn
     */
    function __construct($name, $dsn) {
      $this->dsn= $dsn;
      parent::__construct($name);
    }
      
    /**
     * Setup function
     *
     * @access  public
     * @throws  rdbms.DriverNotSupportedException
     */
    function setUp() {
      try(); {
        $this->conn= &DriverManager::getConnection($this->dsn);
      } if (catch('DriverNotSupportedException', $e)) {
        throw (new PrerequisitesNotMetError(
          PREREQUISITE_INITFAILED,
          $e,
          array(substr($this->dsn, 0, strpos($this->dsn, '://')))
        ));
      }
    }
    
    /**
     * Tear down function
     *
     * @access  public
     */
    function tearDown() {
      $this->conn->close();
    }
    
    /**
     * Test database connect
     *
     * @access  public
     */
    #[@test]
    function testConnect() {
      $result= $this->conn->connect();
      $this->assertTrue($result);
    }
    
    /**
     * Test database select
     *
     * @access  public
     */
    #[@test]
    function testSelect() {
      $r= &$this->conn->query('select %s as version', '$Revision$');
      if ($this->assertSubclass($r, 'rdbms.ResultSet')) {
        $version= $r->next('version');
        $this->assertNotEmpty($version);
        $this->assertEquals($version, '$Revision$');
        return $version;
      }
    }
  }
?>
