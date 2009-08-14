<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'net.xp_framework.unittest.rdbms.integration.AbstractDeadlockTest'
  );

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
?>
