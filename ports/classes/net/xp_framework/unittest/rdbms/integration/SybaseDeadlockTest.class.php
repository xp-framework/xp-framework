<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'net.xp_framework.unittest.rdbms.integration.AbstractDeadlockTest'
  );

  /**
   * Deadlock test on mysql
   *
   */
  class SybaseDeadlockTest extends AbstractDeadlockTest {

    /**
     * Before class method: set minimun server severity;
     * otherwise server messages end up on the error stack
     * and will let the test fail (no error policy).
     *
     */
    #[@beforeClass]
    public static function messageLevel() {
      sybase_min_server_severity(12);
    }


    /**
     * Retrieve DSN
     *
     * @return  string
     */
    public function _dsn() {
      return 'sybase://username:password@servername/tempdb';
    }
  }
?>
