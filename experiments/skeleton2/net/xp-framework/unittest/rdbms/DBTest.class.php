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
    public
      $conn = NULL,
      $dsn  = '';
      
    /**
     * Constructor
     *
     * @access  publuc
     * @param   string name
     * @param   string dsn
     */
    public function __construct($name, $dsn) {
      $this->dsn= $dsn;
      parent::__construct($name);
    }
      
    /**
     * Setup function
     *
     * @access  public
     * @throws  rdbms.DriverNotSupportedException
     */
    public function setUp() {
      try {
        $this->conn= DriverManager::getConnection($this->dsn);
      } catch (DriverNotSupportedException $e) {
        throw  (new PrerequisitesNotMetError(
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
    public function tearDown() {
      $this->conn->close();
    }
    
    /**
     * Test database connect
     *
     * @access  public
     */
    public function testConnect() {
      $result= $this->conn->connect();
      self::assertTrue($result);
    }
    
    /**
     * Test database select
     *
     * @access  public
     */
    public function testSelect() {
      $r= $this->conn->query('select %s as version', '$Revision$');
      if (self::assertSubclass($r, 'rdbms.ResultSet')) {
        $version= $r->next('version');
        self::assertNotEmpty($version);
        self::assertEquals($version, '$Revision$');
        return $version;
      }
    }
  }
?>
