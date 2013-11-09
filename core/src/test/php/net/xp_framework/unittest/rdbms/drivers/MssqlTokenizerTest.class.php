<?php namespace net\xp_framework\unittest\rdbms\drivers;
 
/**
 * Test MSSQL tokenizer
 *
 * @see   xp://rdbms.mssql.MsSQLConnection
 * @see   xp://net.xp_framework.unittest.rdbms.drivers.TDSTokenizerTest
 */
class MssqlTokenizerTest extends TDSTokenizerTest {
    
  /**
   * Sets up a Database Object for the test
   *
   * @return  rdbms.DBConnection
   */
  protected function fixture() {
    return new \rdbms\mssql\MsSQLConnection(new \rdbms\DSN('mssql://localhost:1433/'));
  }
}
