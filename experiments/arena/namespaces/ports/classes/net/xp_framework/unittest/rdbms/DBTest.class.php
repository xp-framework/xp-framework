<?php
/* This class is part of the XP framework
 *
 * $Id: DBTest.class.php 8974 2006-12-27 17:29:09Z friebe $ 
 */

  namespace net::xp_framework::unittest::rdbms;
 
  ::uses(
    'rdbms.DriverManager',
    'unittest.TestCase',
    'net.xp_framework.unittest.rdbms.mock.MockConnection'
  );

  define('MOCK_CONNECTION_CLASS', 'net.xp_framework.unittest.rdbms.mock.MockConnection');

  /**
   * Test rdbms API
   *
   * @purpose  Unit Test
   */
  class DBTest extends unittest::TestCase {
    public
      $conn = NULL;
 
     /**
     * Static initializer
     *
     */  
    public static function __static() {
      rdbms::DriverManager::register('mock', lang::XPClass::forName(MOCK_CONNECTION_CLASS));
    }
     
    /**
     * Setup function
     *
     */
    public function setUp() {
      $this->conn= rdbms::DriverManager::getConnection('mock://mock/MOCKDB');
      $this->assertEquals(0, $this->conn->flags & DB_AUTOCONNECT);
    }
    
    /**
     * Tear down function
     *
     */
    public function tearDown() {
      $this->conn->close();
    }

    /**
     * Asserts a query works
     *
     * @throws  unittest.AssertionFailedError
     */
    protected function assertQuery() {
      $version= '$Revision: 8974 $';
      $this->conn->setResultSet(new MockResultSet(array(array('version' => $version))));
      if (
        ($r= $this->conn->query('select %s as version', $version)) &&
        ($this->assertSubclass($r, 'rdbms.ResultSet')) && 
        ($field= $r->next('version'))
      ) $this->assertEquals($field, $version);
    }

    /**
     * Test database connect
     *
     */
    #[@test]
    public function connect() {
      $result= $this->conn->connect();
      $this->assertTrue($result);
    }

    /**
     * Test database connect throws an SQLConnectException in case it fails
     *
     */
    #[@test, @expect('rdbms.SQLConnectException')]
    public function connectFailure() {
      $this->conn->makeConnectFail('Unknown server');
      $this->conn->connect();
    }
    
    /**
     * Test database select
     *
     */
    #[@test]
    public function select() {
      $this->conn->connect();
      $this->assertQuery();
    }

    /**
     * Test an SQLStateException is thrown if a query is performed on a
     * not yet connect()ed connection object.
     *
     */
    #[@test, @expect('rdbms.SQLStateException')]
    public function queryOnUnConnected() {
      $this->conn->query('select 1');   // Not connected
    }

    /**
     * Test an SQLStateException is thrown if a query is performed on a
     * disconnect()ed connection object.
     *
     */
    #[@test, @expect('rdbms.SQLStateException')]
    public function queryOnDisConnected() {
      $this->conn->connect();
      $this->assertQuery();
      $this->conn->close();
      $this->conn->query('select 1');   // Not connected
    }

    /**
     * Test an SQLConnectionClosedException is thrown if the connection
     * has been lost.
     *
     * @see     rfc://0058
     */
    #[@test, @expect('rdbms.SQLConnectionClosedException')]
    public function connectionLost() {
      $this->conn->connect();
      $this->assertQuery();
      $this->conn->letServerDisconnect();
      $this->conn->query('select 1');   // Not connected
    }

    /**
     * Test an SQLStateException is thrown if a query is performed on a
     * connection thas is not connected due to connect() failure.
     *
     */
    #[@test, @expect('rdbms.SQLStateException')]
    public function queryOnFailedConnection() {
      $this->conn->makeConnectFail('Access denied');
      try {
        $this->conn->connect();
      } catch (rdbms::SQLConnectException $ignored) { }

      $this->conn->query('select 1');   // Previously failed to connect
    }

    /**
     * Test an SQLStatementFailedException is thrown when a query fails.
     *
     */
    #[@test, @expect('rdbms.SQLStatementFailedException')]
    public function statementFailed() {
      $this->conn->connect();
      $this->conn->makeQueryFail('Deadlock', 1205);
      $this->conn->query('select 1');
    }
  }
?>
