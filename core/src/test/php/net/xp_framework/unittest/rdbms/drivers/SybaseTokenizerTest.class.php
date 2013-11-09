<?php namespace net\xp_framework\unittest\rdbms\drivers;
 
/**
 * Test sybase tokenizer
 *
 * @see   xp://rdbms.sybase.SybaseConnection
 * @see   xp://net.xp_framework.unittest.rdbms.drivers.TDSTokenizerTest
 */
class SybaseTokenizerTest extends TDSTokenizerTest {
    
  /**
   * Sets up a Database Object for the test
   *
   * @return  rdbms.DBConnection
   */
  protected function fixture() {
    return new \rdbms\sybase\SybaseConnection(new \rdbms\DSN('sybase://localhost:1999/'));
  }
}
