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
  class MySQLIntegrationTest extends RdbmsIntegrationTest {
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function _dsn() {
      return 'mysql://username:password@servername/tempdb';
    }
  }
?>
