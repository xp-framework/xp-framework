<?php namespace net\xp_framework\unittest\rdbms\drivers;
 
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
    return new \rdbms\sqlsrv\SqlSrvConnection(new \rdbms\DSN('sqlsrv://localhost/'));
  }
}
