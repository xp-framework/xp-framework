<?php namespace net\xp_framework\unittest\rdbms\drivers;
 
/**
 * Test default MySQL tokenizer
 *
 * @see   xp://rdbms.mysql.MySQLConnection
 * @see   xp://net.xp_framework.unittest.rdbms.drivers.MySQLTokenizerTest
 */
class DefaultMySQLTokenizerTest extends MySQLTokenizerTest {
    
  /**
   * Sets up a Database Object for the test
   *
   * @return  rdbms.DBConnection
   */
  protected function fixture() {
    return new \rdbms\mysql\MySQLConnection(new \rdbms\DSN('mysql://localhost/'));
  }
}
