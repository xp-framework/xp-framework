<?php namespace net\xp_framework\unittest\rdbms\drivers;
 
/**
 * Test tokenizers for SQLite connections
 *
 * @see   xp://rdbms.sqlite.SQLiteConnection
 * @see   xp://net.xp_framework.unittest.rdbms.TokenizerTest
 */
class SQLiteTokenizerTest extends \net\xp_framework\unittest\rdbms\TokenizerTest {

  /**
   * Sets up a Database Object for the test
   *
   * @return  rdbms.DBConnection
   */
  protected function fixture() {
    return new \rdbms\sqlite\SQLiteConnection(new \rdbms\DSN('sqlite://localhost/'));
  }
}
