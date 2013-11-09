<?php namespace net\xp_framework\unittest\rdbms\drivers;
 
/**
 * Test tokenizers for IBase connections
 *
 * @see   xp://rdbms.ibase.InterBaseConnection
 * @see   xp://net.xp_framework.unittest.rdbms.TokenizerTest
 */
class InterBaseTokenizerTest extends \net\xp_framework\unittest\rdbms\TokenizerTest {

  /**
   * Sets up a Database Object for the test
   *
   * @return  rdbms.DBConnection
   */
  protected function fixture() {
    return new \rdbms\ibase\InterBaseConnection(new \rdbms\DSN('ibase://localhost/'));
  }
}
