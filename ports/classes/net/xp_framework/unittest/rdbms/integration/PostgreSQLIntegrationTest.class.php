<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_framework.unittest.rdbms.integration.RdbmsIntegrationTest');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class PostgreSQLIntegrationTest extends RdbmsIntegrationTest {
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function _dsn() {
      return 'pgsql://username:password@servername/public';
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    protected function createAutoIncrementTable($name) {
      $this->removeTable($name);
      $this->db()->query('create table %c (pk serial primary key, username varchar(30))', $name);
    }
  }
?>
