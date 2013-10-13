<?php namespace net\xp_framework\unittest\rdbms\drivers;
 
use rdbms\sqlsrv\SqlSrvConnection;


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
    return new SqlSrvConnection(new \rdbms\DSN('sqlsrv://localhost/'));
  }
}
