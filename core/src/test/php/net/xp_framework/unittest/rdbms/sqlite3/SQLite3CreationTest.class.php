<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'rdbms.sqlite3.SQLite3Connection'
  );

  /**
   * Testcase for rdbms.sqlite3.SQLite3Connection
   *
   * @see      xp://rdbms.sqlite3.SQLite3Connection
   */
  class SQLite3CreationTest extends TestCase {

    /**
     * Test
     *
     */
    #[@test]
    public function connect_dot() {
      $conn= new SQLite3Connection(new DSN('sqlite+3://./:memory:'));
      $conn->connect();
    }

    /**
     * Test
     *
     */
    #[@test @throws('rdbms.SQLConnectException')]
    public function connect_persistent_is_not_supported() {
      $conn= new SQLite3Connection(new DSN('sqlite+3://./:memory:/?persistent=1'));
      $conn->connect();
    }

    /**
     * Test
     *
     */
    #[@test, @expect('rdbms.SQLConnectException')]
    public function connect_does_not_support_remote_hosts() {
      $conn= new SQLite3Connection(new DSN('sqlite+3://some.host/:memory:'));
      $conn->connect();
    }

    /**
     * Test invalid filenames
     *
     * @see   https://github.com/xp-framework/xp-framework/issues/107
     */
    #[@test, @expect('rdbms.SQLConnectException')]
    public function connect_fails_for_invalid_filenames() {
      $conn= new SQLite3Connection(new DSN('sqlite+3://./:database.db'));
      $conn->connect();
    }

    /**
     * Test stream wrappers don't work
     *
     * @see   https://bugs.php.net/bug.php?id=55154
     */
    #[@test, @expect('rdbms.SQLConnectException')]
    public function connect_does_not_support_streams() {
      $conn= new SQLite3Connection(new DSN('sqlite+3://./res://database.db'));
      $conn->connect();
    }
  }
?>
