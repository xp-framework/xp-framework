<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_framework.unittest.rdbms.integration.RdbmsIntegrationTest');

  /**
   * MySQL integration test
   *
   * @ext       mysql
   */
  class MySQLIntegrationTest extends RdbmsIntegrationTest {
    
    /**
     * Retrieve dsn
     *
     * @return  string
     */
    public function _dsn() {
      return 'mysql';
    }
    
    /**
     * Create autoincrement table
     *
     * @param   string name
     */
    protected function createAutoIncrementTable($name) {
      $this->removeTable($name);
      $this->db()->query('create table %c (pk int primary key auto_increment, username varchar(30))', $name);
    }
  }
?>
