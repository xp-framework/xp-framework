<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'rdbms.sqlite.SQLiteConnection',
    'net.xp_framework.unittest.rdbms.TokenizerTest'
  );

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
      return new SQLiteConnection(new DSN('sqlite://localhost/'));
    }
  }
?>
