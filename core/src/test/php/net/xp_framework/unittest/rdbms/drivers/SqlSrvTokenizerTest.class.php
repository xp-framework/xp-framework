<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'rdbms.sqlsrv.SqlSrvConnection',
    'net.xp_framework.unittest.rdbms.drivers.TDSTokenizerTest'
  );

  /**
   * Test SqlSrv tokenizer
   *
   * @see   xp://rdbms.sqlsrv.SqlSrvConnection
   * @see   xp://net.xp_framework.unittest.rdbms.drivers.TDSTokenizerTest
   */
  class SqlSrvTokenizerTest extends TDSTokenizerTest {
      
    /**
     * Sets up a Database Object for the test
     *
     * @return  rdbms.DBConnection
     */
    protected function fixture() {
      return new SqlSrvConnection(new DSN('sqlsrv://localhost/'));
    }
  }
?>
