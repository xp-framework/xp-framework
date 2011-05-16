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
        if (FALSE !== ($p= strpos($name, '.'))) {
          $cm->queue($dsn, substr($name, 0, $p), substr($name, $p+ 1));
        } else {
          $cm->queue($dsn, $name);
        }
      }
      return $cm;
    }
    
    /**
     * Test queue() method returns a DSN instance
     *
     */
    #[@test]
    public function queueReturnsDSN() {
      $dsn= 'mock://user:pass@host/db';
      $cm= $this->instanceWith(array());
      
      $this->assertEquals(new DSN($dsn), $cm->queue($dsn));
    }
 
    /**
     * Test queue() method returns a DSN instance
     *
     */
    #[@test]
    public function queueReturnsDSNWhenPreviouslyRegistered() {
      $dsn= 'mock://user:pass@host/db';
      $cm= $this->instanceWith(array());
      $cm->queue($dsn);

      $this->assertEquals(new DSN($dsn), $cm->queue($dsn));
    }

    /**
     * Test queue() method implementation
     *
     */
    #[@test]
    public function queueOverwritesPreviouslyRegistered() {
      $conn1= 'mock://user:pass@host/db1';
      $conn2= 'mock://user:pass@host/db2';
      $cm= $this->instanceWith(array());

      $this->assertEquals(new DSN($conn1), $cm->queue($conn1));
      $this->assertEquals(new DSN($conn2), $cm->queue($conn2));

      $this->assertEquals(new DSN($conn2), $cm->getByHost('host', 0)->dsn);
    }
  }
?>
