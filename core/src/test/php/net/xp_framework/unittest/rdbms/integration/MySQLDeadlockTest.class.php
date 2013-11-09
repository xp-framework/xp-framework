<?php namespace net\xp_framework\unittest\rdbms\integration;

/**
 * Deadlock test on mysql
 *
 */
class MySQLDeadlockTest extends AbstractDeadlockTest {

  /**
   * Retrieve DSN
   *
   * @return  string
   */
  public function _dsn() {
    return 'mysql';
  }
}
