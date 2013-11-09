<?php namespace net\xp_framework\unittest\rdbms\sqlite3;

use rdbms\sqlite3\SQLite3Connection;

/**
 * Testcase for rdbms.sqlite3.SQLite3Connection
 *
 * @see   xp://rdbms.sqlite3.SQLite3Connection
 * @see   https://github.com/xp-framework/xp-framework/issues/107
 * @see   https://github.com/xp-framework/xp-framework/issues/111
 * @see   https://bugs.php.net/bug.php?id=55154
 */
#[@action(new \unittest\actions\ExtensionAvailable('sqlite3'))]
class SQLite3CreationTest extends \unittest\TestCase {

  #[@test]
  public function connect_dot() {
    $conn= new SQLite3Connection(new \rdbms\DSN('sqlite+3://./:memory:'));
    $conn->connect();
  }

  #[@test, @expect('rdbms.SQLConnectException')]
  public function connect_persistent_is_not_supported() {
    $conn= new SQLite3Connection(new \rdbms\DSN('sqlite+3://./:memory:/?persistent=1'));
    $conn->connect();
  }

  #[@test, @expect('rdbms.SQLConnectException')]
  public function connect_does_not_support_remote_hosts() {
    $conn= new SQLite3Connection(new \rdbms\DSN('sqlite+3://some.host/:memory:'));
    $conn->connect();
  }

  #[@test, @expect('rdbms.SQLConnectException')]
  public function connect_fails_for_invalid_filenames() {
    $conn= new SQLite3Connection(new \rdbms\DSN('sqlite+3://./'));
    $conn->connect();
  }

  #[@test, @expect('rdbms.SQLConnectException')]
  public function connect_does_not_support_streams() {
    $conn= new SQLite3Connection(new \rdbms\DSN('sqlite+3://./res://database.db'));
    $conn->connect();
  }
}
