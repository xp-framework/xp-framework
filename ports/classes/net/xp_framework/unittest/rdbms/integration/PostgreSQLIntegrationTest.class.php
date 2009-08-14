<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_framework.unittest.rdbms.integration.RdbmsIntegrationTest');

  /**
   * PostgreSQL integration test
   *
   * @ext       pgsql
   */
  class PostgreSQLIntegrationTest extends RdbmsIntegrationTest {
    
    /**
     * Retrieve dsn
     *
     * @return  string
     */
    public function _dsn() {
      return 'pgsql';
    }
    
    /**
     * Create autoincrement table
     *
     * @param   string name
     */
    protected function createAutoIncrementTable($name) {
      $this->removeTable($name);
      $this->db()->query('create table %c (pk serial primary key, username varchar(30))', $name);
    }
  }
?>
