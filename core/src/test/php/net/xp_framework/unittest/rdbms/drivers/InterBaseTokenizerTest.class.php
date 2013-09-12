<?php namespace net\xp_framework\unittest\rdbms\drivers;
 
use rdbms\ibase\InterBaseConnection;
use net\xp_framework\unittest\rdbms\TokenizerTest;


/**
 * Test tokenizers for IBase connections
 *
 * @see   xp://rdbms.ibase.InterBaseConnection
 * @see   xp://net.xp_framework.unittest.rdbms.TokenizerTest
 */
class InterBaseTokenizerTest extends TokenizerTest {

  /**
   * Sets up a Database Object for the test
   *
   * @return  rdbms.DBConnection
   */
  protected function fixture() {
    return new InterBaseConnection(new \rdbms\DSN('ibase://localhost/'));
  }
}
