<?php namespace net\xp_framework\unittest\rdbms;

use unittest\TestCase;
use rdbms\ConnectionManager;

/**
 * ConnectionManager testcase
 *
 * @see   xp://rdbms.ConnectionManager
 */
abstract class ConnectionManagerTest extends TestCase {
  
  /**
   * Empties connection manager pool
   */
  public function setUp() {
    ConnectionManager::getInstance()->pool= array();
  }
  
  /**
   * Returns an instance with a given number of DSNs
   *
   * @param   [:string] dsns
   * @return  rdbms.ConnectionManager
   */
  protected abstract function instanceWith($dsns);

  #[@test]
  public function initallyEmpty() {
    $this->assertEquals(array(), $this->instanceWith(array())->getConnections());
  }

  #[@test]
  public function acquireExistingConnectionViaGetByHost() {
    $cm= $this->instanceWith(array('mydb' => 'mock://user:pass@host/db?autoconnect=1'));
    $this->assertInstanceOf('rdbms.DBConnection', $cm->getByHost('mydb', 0));
  }
  
  #[@test, @expect('rdbms.ConnectionNotRegisteredException')]
  public function acquireNonExistantConnectionViaGetByHost() {
    $cm= $this->instanceWith(array('mydb' => 'mock://user:pass@host/db?autoconnect=1'));
    $cm->getByHost('nonexistant', 0);
  }

  #[@test]
  public function acquireExistingConnectionViaGet() {
    $cm= $this->instanceWith(array('mydb' => 'mock://user:pass@host/db?autoconnect=1'));
    $this->assertInstanceOf('rdbms.DBConnection', $cm->getByHost('mydb', 0));
  }
  
  #[@test, @expect('rdbms.ConnectionNotRegisteredException')]
  public function acquireNonExistantConnectionWithExistantUserViaGet() {
    $cm= $this->instanceWith(array('mydb' => 'mock://user:pass@host/db?autoconnect=1'));
    $cm->get('nonexistant', 'user');
  }

  #[@test, @expect('rdbms.ConnectionNotRegisteredException')]
  public function acquireExistantConnectionWithNonExistantUserViaGet() {
    $cm= $this->instanceWith(array('mydb' => 'mock://user:pass@host/db?autoconnect=1'));
    $cm->get('mydb', 'nonexistant');
  }

  #[@test]
  public function invalidDsnScheme() {
    $this->instanceWith(array('mydb' => 'invalid://user:pass@host/db?autoconnect=1'));
  }
  
  #[@test, @expect('rdbms.DriverNotSupportedException')]
  public function acquireInvalidDsnScheme() {
    $cm= $this->instanceWith(array('mydb' => 'invalid://user:pass@host/db?autoconnect=1'));
    $cm->getByHost('mydb', 0);
  }

  #[@test]
  public function getByUserAndHost() {
    $dsns= array(
      'mydb.user'  => 'mock://user:pass@host/db?autoconnect=1',
      'mydb.admin' => 'mock://admin:pass@host/db?autoconnect=1'
    );
    $cm= $this->instanceWith($dsns);
    $this->assertEquals(new \rdbms\DSN($dsns['mydb.user']), $cm->get('mydb', 'user')->dsn);
  }
 
  #[@test]
  public function getFirstByHost() {
    $dsns= array(
      'mydb.user'  => 'mock://user:pass@host/db?autoconnect=1',
      'mydb.admin' => 'mock://admin:pass@host/db?autoconnect=1'
    );
    $cm= $this->instanceWith($dsns);
    $this->assertEquals(new \rdbms\DSN($dsns['mydb.user']), $cm->getByHost('mydb', 0)->dsn);
  }
 
  #[@test]
  public function getSecondByHost() {
    $dsns= array(
      'mydb.user'  => 'mock://user:pass@host/db?autoconnect=1',
      'mydb.admin' => 'mock://admin:pass@host/db?autoconnect=1'
    );
    $cm= $this->instanceWith($dsns);
    $this->assertEquals(new \rdbms\DSN($dsns['mydb.admin']), $cm->getByHost('mydb', 1)->dsn);
  }

  #[@test]
  public function getAllByHost() {
    $dsns= array(
      'mydb.user'  => 'mock://user:pass@host/db?autoconnect=1',
      'mydb.admin' => 'mock://admin:pass@host/db?autoconnect=1'
    );
    $cm= $this->instanceWith($dsns);
    
    $values= array();
    foreach ($cm->getByHost('mydb') as $conn) {
      $values[]= $conn->dsn;
    }
    $this->assertEquals(
      array(new \rdbms\DSN($dsns['mydb.user']), new \rdbms\DSN($dsns['mydb.admin'])), 
      $values
    );
  }
}
