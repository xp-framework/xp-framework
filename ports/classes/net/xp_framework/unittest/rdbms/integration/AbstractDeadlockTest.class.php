<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'lang.Runtime',
    'rdbms.DriverManager'
  );

  /**
   * Abstract deadlock test
   *
   */
  abstract class AbstractDeadlockTest extends TestCase {
  
    /**
     * Retrieve DSN
     *
     * @return  string
     */
    abstract public function _dsn();
    
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      try {
        $this->dropTables();
        $this->createTables();
      } catch (Throwable $e) {
        throw new PrerequisitedNotMetError($e->getMessage(), $e);
      }
    }
    
    /**
     * Tear down test case
     *
     */
    public function tearDown() {
      $this->dropTables();
    }
    
    /**
     * Create necessary tables for this test
     *
     */
    protected function createTables() {
      $db= DriverManager::getConnection($this->_dsn());
      $db->connect();
      
      $db->query('create table table_a (pk int)');
      $db->query('create table table_b (pk int)');
      
      $db->insert('table_a values (1)');
      $db->insert('table_a values (2)');

      $db->insert('table_b values (1)');
      $db->insert('table_b values (2)');
      
      $db->close();
    }
    
    /**
     * Cleanup database tables
     *
     */
    protected function dropTables() {
      $db= DriverManager::getConnection($this->_dsn());
      $db->connect();
      
      $db->query('drop table if exists table_a');
      $db->query('drop table if exists table_b');
      
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
          array('net.xp_framework.unittest.rdbms.integration.SQLRunner', $this->_dsn())
        ));
        $this->assertEquals('! Started', $proc->out->readLine());
        return $proc;
      }
    }
    
    /**
     * Test a deadlock situation results in rdbms.SQLDeadlockException
     * being thrown.
     *
     */
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
?>
