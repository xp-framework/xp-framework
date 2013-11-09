<?php namespace net\xp_framework\unittest\rdbms;

use rdbms\DSN;
use rdbms\ConnectionManager;

/**
 * Tests for connection managers with connections programmatically
 * registered.
 *
 * @see   xp://rdbms.ConnectionManager#queue
 * @see   xp://net.xp_framework.unittest.rdbms.ConnectionManagerTest
 */
#[@action(new \net\xp_framework\unittest\rdbms\mock\RegisterMockConnection())]
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
      if (false !== ($p= strpos($name, '.'))) {
        $cm->queue($dsn, substr($name, 0, $p), substr($name, $p+ 1));
      } else {
        $cm->queue($dsn, $name);
      }
    }
    return $cm;
  }
  
  #[@test]
  public function queueReturnsDSN() {
    $dsn= 'mock://user:pass@host/db';
    $cm= $this->instanceWith(array());
    
    $this->assertEquals(new DSN($dsn), $cm->queue($dsn));
  }
 
  #[@test]
  public function queueReturnsDSNWhenPreviouslyRegistered() {
    $dsn= 'mock://user:pass@host/db';
    $cm= $this->instanceWith(array());
    $cm->queue($dsn);

    $this->assertEquals(new DSN($dsn), $cm->queue($dsn));
  }

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
