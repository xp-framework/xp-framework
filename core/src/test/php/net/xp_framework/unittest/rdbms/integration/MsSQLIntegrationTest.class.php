<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_framework.unittest.rdbms.integration.RdbmsIntegrationTest');

  /**
   * MSSQL integration test
   *
   * @ext       mssql
   */
  class MsSQLIntegrationTest extends RdbmsIntegrationTest {
    
    /**
     * Before class method: set minimun server severity;
     * otherwise server messages end up on the error stack
     * and will let the test fail (no error policy).
     *
     */
    public function setUp() {
      parent::setUp();
      if (function_exists('mssql_min_message_severity')) {
        mssql_min_message_severity(12);
      }
    }
          

    /**
     * Creates table name
     *
     * @return  string
     */
    protected function tableName() {
      return '#unittest';
    }

    /**
     * Retrieve dsn
     *
     * @return  string
     */
    public function _dsn() {
      return 'mssql';
    }
    
    /**
     * Create autoincrement table
     *
     * @param   string name
     */
    protected function createAutoIncrementTable($name) {
      $this->removeTable($name);
      $this->db()->query('create table %c (pk int identity, username varchar(30))', $name);
    }
    
    /**
     * Create transactions table
     *
     * @param   string name
     */
    protected function createTransactionsTable($name) {
      $this->removeTable($name);
      $this->db()->query('create table %c (pk int, username varchar(30))', $name);
    }

    /**
     * Test selecting date values returns util.Date objects
     *
     */
    #[@test]
    public function selectDate() {
      $cmp= new Date('2009-08-14 12:45:00');
      $result= $this->db()->query('select convert(datetime, %s, 120) as value', $cmp)->next('value');

      $this->assertInstanceOf('util.Date', $result);
      $this->assertEquals($cmp->toString('Y-m-d'), $result->toString('Y-m-d'));
    }
  }
?>
