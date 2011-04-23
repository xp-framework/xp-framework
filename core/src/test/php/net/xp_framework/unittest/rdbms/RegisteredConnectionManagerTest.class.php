<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'net.xp_framework.unittest.rdbms.ConnectionManagerTest',
    'rdbms.DriverManager'
  );

  /**
   * Tests for connection managers with connections programmatically
   * registered.
   *
   * @see   xp://rdbms.ConnectionManager#register
   * @see   xp://net.xp_framework.unittest.rdbms.ConnectionManagerTest
   */
  class RegisteredConnectionManagerTest extends ConnectionManagerTest {

    /**
     * Returns an instance with a given number of DSNs
     *
     * @param   [:string] dsns
     * @return  rdbms.ConnectionManager
     */
    protected function instanceWith($dsns) {
      $cm= ConnectionManager::getInstance();
      foreach ($dsns as $name => $dsn) {
        $cm->register(DriverManager::getConnection($dsn), $name);
      }
      return $cm;
    }

    /**
     * Check that configuring with a not supported scheme works.
     *
     */
    #[@test, @ignore('Does not work in this class as we eagerly create connections in instanceWith()')]
    public function invalidDsnScheme() {
      // NOOP
    }
  }
?>
