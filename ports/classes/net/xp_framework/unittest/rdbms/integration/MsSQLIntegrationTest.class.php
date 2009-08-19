<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_framework.unittest.rdbms.integration.RdbmsIntegrationTest');

  /**
   * Sybase integration test
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
  }
?>
