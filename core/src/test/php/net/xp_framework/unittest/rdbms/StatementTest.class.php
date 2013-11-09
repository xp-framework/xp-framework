<?php namespace net\xp_framework\unittest\rdbms;
 
use unittest\TestCase;
use rdbms\Statement;
use rdbms\DriverManager;
use net\xp_framework\unittest\rdbms\dataset\Job;

/**
 * Test Statement class
 *
 * @see   xp://rdbms.Statement
 */
#[@action(new \net\xp_framework\unittest\rdbms\mock\RegisterMockConnection())]
class StatementTest extends TestCase {
  public $conn= null;
  public $peer= null;

  /**
   * Setup method
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

  #[@test]
  public function simpleStatement() {
    $this->assertStatement('select * from job', new Statement('select * from job'));
  }
  
  #[@test]
  public function tokenizedStatement() {
    $this->assertStatement(
      'select * from job where job_id= 1',
      new Statement('select * from job where job_id= %d', 1)
    );
  }
  
  #[@test]
  public function multiTokenStatement() {
    $this->assertStatement(
      'select * from job where job_id= 1 and title= "Test"',
      new Statement('select * from job where job_id= %d and title= %s', 1, 'Test')
    );
  }
}
