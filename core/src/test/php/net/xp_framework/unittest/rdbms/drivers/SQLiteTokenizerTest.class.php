<?php namespace net\xp_framework\unittest\rdbms\drivers;
 
use rdbms\sqlite\SQLiteConnection;
use net\xp_framework\unittest\rdbms\TokenizerTest;


/**
 * Test tokenizers for SQLite connections
 *
 * @see   xp://rdbms.sqlite.SQLiteConnection
 * @see   xp://net.xp_framework.unittest.rdbms.TokenizerTest
 */
class SQLiteTokenizerTest extends TokenizerTest {

  /**
   * Sets up a Database Object for the test
   *
   * @return  rdbms.DBConnection
   */
  protected function fixture() {
    return new SQLiteConnection(new \rdbms\DSN('sqlite://localhost/'));
  }
}
