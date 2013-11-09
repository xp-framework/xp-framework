<?php namespace net\xp_framework\unittest\rdbms\integration;

use unittest\TestCase;
use lang\Runtime;
use rdbms\DriverManager;

/**
 * Abstract deadlock test
 *
 */
abstract class AbstractDeadlockTest extends TestCase {
  protected $dsn= null;

  /**
   * Retrieve DSN
   *
   * @return  string
   */
  abstract public function _dsn();

  /**
   * Retrieve database connection object
   *
   * @param   bool connect default TRUE
   * @return  rdbms.DBConnection
   */
  protected function db($connect= true) {
    with ($db= DriverManager::getConnection($this->dsn)); {
      if ($connect) $db->connect();
      return $db;
    }
  }
  
  /**
   * Sets up test case
   *
   */
  public function setUp() {
    $this->dsn= \util\Properties::fromString($this->getClass()->getPackage()->getResource('database.ini'))->readString(
      $this->_dsn(),
      'dsn',
      null
    );

    if (null === $this->dsn) {
      throw new \unittest\PrerequisitesNotMetError('No credentials for '.$this->getClassName());
    }

    try {
      $this->dropTables();
      $this->createTables();
    } catch (\lang\Throwable $e) {
      throw new \unittest\PrerequisitesNotMetError($e->getMessage(), $e);
    }
  }
  
  /**
   * Tear down test case
   */
  public function tearDown() {
    $this->dropTables();
  }
  
  /**
   * Create necessary tables for this test
   */
  protected function createTables() {
    $db= $this->db();
    
    $db->query('create table table_a (pk int)');
    $db->query('create table table_b (pk int)');
    
    $db->insert('into table_a values (1)');
    $db->insert('into table_a values (2)');

    $db->insert('into table_b values (1)');
    $db->insert('into table_b values (2)');
    
    $db->close();
  }
  
  /**
   * Cleanup database tables
   */
  protected function dropTables() {
    $db= $this->db();
    
    try {
      $db->query('drop table table_a');
    } catch (\rdbms\SQLStatementFailedException $ignored) {}
    
    try {
      $db->query('drop table table_b');
    } catch (\rdbms\SQLStatementFailedException $ignored) {}
    
    $db->close();
  }
  
  /**
   * Start new SQLRunner process
   *
   * @return  lang.Process
   */
  protected function newProcess() {
    with ($rt= Runtime::getInstance()); {
      $proc= $rt->getExecutable()->newInstance(array_merge(
        $rt->startupOptions()->asArguments(),
        array($rt->bootstrapScript('class')),
        array('net.xp_framework.unittest.rdbms.integration.SQLRunner', $this->dsn)
      ));
      $this->assertEquals('! Started', $proc->out->readLine());
      return $proc;
    }
  }

  #[@test]
  public function provokeDeadlock() {
    $a= $this->newProcess();
    $b= $this->newProcess();
    
    $a->in->write("update table_a set pk= pk+1\n");
    $b->in->write("update table_b set pk= pk+1\n");
    
    // Reads "+ OK", on each process
    $a->out->readLine();
    $b->out->readLine();
    
    // Now, process a hangs, waiting for lock to table_b
    $a->in->write("update table_b set pk= pk+1\n");
    
    // Finalize the deadlock situation, so the database can
    // detect it.
    $b->in->write("update table_a set pk= pk+1\n");
    
    $a->in->close();
    $b->in->close();
    
    $result= array(
      $a->out->readLine(),
      $b->out->readLine()
    );
    sort($result);
    
    // Cleanup
    $a->close(); $b->close();
    
    // Assert one process succeeds, the other catches a deadlock exception
    // We can't tell which one will do what, though.
    $this->assertEquals(array('+ OK', '- rdbms.SQLDeadlockException'), $result);
  }
}
