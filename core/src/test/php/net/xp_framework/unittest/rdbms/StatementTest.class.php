<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'unittest.TestCase',
    'rdbms.Statement',
    'rdbms.DriverManager',
    'net.xp_framework.unittest.rdbms.dataset.Job'
  );

  /**
   * Test Statement class
   *
   * @see      xp://rdbms.Statement
   * @purpose  Unit Test
   */
  class StatementTest extends TestCase {
    const
      MOCK_CONNECTION_CLASS = 'net.xp_framework.unittest.rdbms.mock.MockConnection';

    public
      $conn = NULL,
      $peer = NULL;

    /**
     * Mock connection registration
     *
     */  
    #[@beforeClass]
    public static function registerMockConnection() {
      DriverManager::register('mock', XPClass::forName(self::MOCK_CONNECTION_CLASS));
    }

    /**
     * Setup method
     *
     */
    public function setUp() {
      $this->conn= DriverManager::getConnection('mock://mock/JOBS?autoconnect=1');
      $this->peer= Job::getPeer();
      $this->peer->setConnection(DriverManager::getConnection('mock://mock/JOBS?autoconnect=1'));
    }
    
    /**
     * Helper method that will call executeSelect() on the passed statement and
     * compare the resulting string to the expected string.
     *
     * @param   string sql
     * @param   rdbms.Statement statement
     * @throws  unittest.AssertionFailedError
     */
    protected function assertStatement($sql, $statement) {
      $statement->executeSelect($this->conn, $this->peer);
      $this->assertEquals($sql, trim($this->conn->getStatement(), ' '));
    }
    
    /**
     * Test simple statement
     *
     */
    #[@test]
    public function simpleStatement() {
      $this->assertStatement('select * from job', new Statement('select * from job'));
    }
    
    /**
     * Test tokenized statement
     *
     */
    #[@test]
    public function tokenizedStatement() {
      $this->assertStatement(
        'select * from job where job_id= 1',
        new Statement('select * from job where job_id= %d', 1)
      );
    }
    
    /**
     * Test multi-token statement
     *
     */
    #[@test]
    public function multiTokenStatement() {
      $this->assertStatement(
        'select * from job where job_id= 1 and title= "Test"',
        new Statement('select * from job where job_id= %d and title= %s', 1, 'Test')
      );
    }
  }
?>
