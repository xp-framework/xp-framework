<?php namespace net\xp_framework\unittest\rdbms\integration;

/**
 * Deadlock test on PostgreSQL
 *
 */
class PostgreSQLDeadlockTest extends AbstractDeadlockTest {

  /**
   * Retrieve DSN
   *
   * @return  string
   */
  public function _dsn() {
    return 'pgsql';
  }
}
