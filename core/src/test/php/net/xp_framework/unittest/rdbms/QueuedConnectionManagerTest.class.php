<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'net.xp_framework.unittest.rdbms.ConnectionManagerTest',
    'rdbms.DSN'
  );

  /**
   * Tests for connection managers with connections programmatically
   * registered.
   *
   * @see   xp://rdbms.ConnectionManager#queue
   * @see   xp://net.xp_framework.unittest.rdbms.ConnectionManagerTest
   */
  class QueuedConnectionManagerTest extends ConnectionManagerTest {

    /**
     * Returns an instance with a given number of DSNs
     *
     * @param   [:string] dsns
     * @return  rdbms.ConnectionManager
     */
    protected function instanceWith($dsns) {
      $cm= ConnectionManager::getInstance();
      foreach ($dsns as $name => $dsn) {
        $cm->queue($dsn, $name);
      }
      return $cm;
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function queueReturnsDSN() {
      $dsn= 'mock://user:pass@host/db?autoconnect=1';
      $this->assertEquals(
        new DSN($dsn), 
        ConnectionManager::getInstance()->queue($dsn)
      );
    }
  }
?>
