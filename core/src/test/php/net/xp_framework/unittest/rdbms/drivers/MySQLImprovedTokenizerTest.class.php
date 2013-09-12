<?php namespace net\xp_framework\unittest\rdbms\drivers;
 
use rdbms\mysqli\MySQLiConnection;


/**
 * Test MySQLi tokenizer
 *
 * @see   xp://rdbms.mysqli.MySQLiConnection
 * @see   xp://net.xp_framework.unittest.rdbms.drivers.MySQLTokenizerTest
 */
class MySQLImprovedTokenizerTest extends MySQLTokenizerTest {
    
  /**
   * Sets up a Database Object for the test
   *
   * @return  rdbms.DBConnection
   */
  protected function fixture() {
    return new MySQLiConnection(new \rdbms\DSN('mysqli://localhost/'));
  }
}
