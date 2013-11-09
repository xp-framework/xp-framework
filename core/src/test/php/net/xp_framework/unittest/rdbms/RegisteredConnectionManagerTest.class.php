<?php namespace net\xp_framework\unittest\rdbms;

use rdbms\DriverManager;
use rdbms\ConnectionManager;

/**
 * Tests for connection managers with connections programmatically
 * registered.
 *
 * @see   xp://rdbms.ConnectionManager#register
 * @see   xp://net.xp_framework.unittest.rdbms.ConnectionManagerTest
 */
#[@action(new \net\xp_framework\unittest\rdbms\mock\RegisterMockConnection())]
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
      $conn= DriverManager::getConnection($dsn);
      if (false !== ($p= strpos($name, '.'))) {
        $cm->register($conn, substr($name, 0, $p), substr($name, $p+ 1));
      } else {
        $cm->register($conn, $name);
      }
    }
    return $cm;
  }

  #[@test, @ignore('Does not work in this class as we eagerly create connections in instanceWith()')]
  public function invalidDsnScheme() {
    // NOOP
  }

  #[@test]
  public function registerReturnsConnection() {
    $conn= DriverManager::getConnection('mock://user:pass@host/db');
    $cm= $this->instanceWith(array());
    
    $this->assertEquals($conn, $cm->register($conn));
  }
 
  #[@test]
  public function registerReturnsConnectionWhenPreviouslyRegistered() {
    $conn= DriverManager::getConnection('mock://user:pass@host/db');
    $cm= $this->instanceWith(array());
    $cm->register($conn);

    $this->assertEquals($conn, $cm->register($conn));
  }

  #[@test]
  public function registerOverwritesPreviouslyRegistered() {
    $conn1= DriverManager::getConnection('mock://user:pass@host/db1');
    $conn2= DriverManager::getConnection('mock://user:pass@host/db2');
    $cm= $this->instanceWith(array());

    $this->assertEquals($conn1, $cm->register($conn1));
    $this->assertEquals($conn2, $cm->register($conn2));

    $this->assertEquals($conn2, $cm->getByHost('host', 0));
  }
}
