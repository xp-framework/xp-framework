<?php namespace net\xp_framework\unittest\rdbms\integration;

/**
 * Deadlock test on Sybase
 *
 * @ext  sybase_ct
 */
class SybaseDeadlockTest extends AbstractDeadlockTest {

  /**
   * Before class method: set minimun server severity;
   * otherwise server messages end up on the error stack
   * and will let the test fail (no error policy).
   */
  public function setUp() {
    parent::setUp();
    if (function_exists('sybase_min_server_severity')) {
      sybase_min_server_severity(12);
    }
  }

  /**
   * Retrieve DSN
   *
   * @return  string
   */
  public function _dsn() {
    return 'sybase';
  }
}
