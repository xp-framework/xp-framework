<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'rdbms.ConnectionManager',
    'net.xp_framework.unittest.rdbms.mock.MockConnection'
  );

  /**
   * ConnectionManager testcase
   *
   * @see   xp://rdbms.ConnectionManager
   */
  abstract class ConnectionManagerTest extends TestCase {
    const MOCK_CONNECTION_CLASS = 'net.xp_framework.unittest.rdbms.mock.MockConnection';
  
    /**
     * Mock connection registration
     *
     */  
    #[@beforeClass]
    public static function registerMockConnection() {
      DriverManager::register('mock', XPClass::forName(self::MOCK_CONNECTION_CLASS));
    }
    
    /**
     * Empties connection manager pool
     *
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

    /**
     * Check configure with an empty properties file yields an an empty
     * connection manager instance pool.
     *
     */
    #[@test]
    public function initallyEmpty() {
      $this->assertEquals(array(), $this->instanceWith(array())->getConnections());
    }

    /**
     * Acquire an existing connection
     *
     */
    #[@test]
    public function acquireExistingConnectionViaGetByHost() {
      $cm= $this->instanceWith(array('mydb' => 'mock://user:pass@host/db?autoconnect=1'));
      $this->assertInstanceOf(self::MOCK_CONNECTION_CLASS, $cm->getByHost('mydb', 0));
    }
    
    /**
     * Try to acquire a non-existant connection
     *
     */
    #[@test, @expect('rdbms.ConnectionNotRegisteredException')]
    public function acquireNonExistantConnectionViaGetByHost() {
      $cm= $this->instanceWith(array('mydb' => 'mock://user:pass@host/db?autoconnect=1'));
      $cm->getByHost('nonexistant', 0);
    }

    /**
     * Acquire an existing connection
     *
     */
    #[@test]
    public function acquireExistingConnectionViaGet() {
      $cm= $this->instanceWith(array('mydb' => 'mock://user:pass@host/db?autoconnect=1'));
      $this->assertInstanceOf(self::MOCK_CONNECTION_CLASS, $cm->getByHost('mydb', 0));
    }
    
    /**
     * Try to acquire a non-existant connection
     *
     */
    #[@test, @expect('rdbms.ConnectionNotRegisteredException')]
    public function acquireNonExistantConnectionWithExistantUserViaGet() {
      $cm= $this->instanceWith(array('mydb' => 'mock://user:pass@host/db?autoconnect=1'));
      $cm->get('nonexistant', 'user');
    }

    /**
     * Try to acquire a non-existant connection
     *
     */
    #[@test, @expect('rdbms.ConnectionNotRegisteredException')]
    public function acquireExistantConnectionWithNonExistantUserViaGet() {
      $cm= $this->instanceWith(array('mydb' => 'mock://user:pass@host/db?autoconnect=1'));
      $cm->get('mydb', 'nonexistant');
    }

    /**
     * Check that configuring with a not supported scheme works.
     *
     */
    #[@test]
    public function invalidDsnScheme() {
      $this->instanceWith(array('mydb' => 'invalid://user:pass@host/db?autoconnect=1'));
    }
    
    /**
     * Acquiring an unsupported connection should throw a
     * rdbms.DriverNotSupportedException
     *
     */
    #[@test, @expect('rdbms.DriverNotSupportedException')]
    public function acquireInvalidDsnScheme() {
      $cm= $this->instanceWith(array('mydb' => 'invalid://user:pass@host/db?autoconnect=1'));
      $cm->getByHost('mydb', 0);
    }

    /**
     * Acquire a connection by host
     *
     */
    #[@test]
    public function getByUserAndHost() {
      $dsns= array(
        'mydb.user'  => 'mock://user:pass@host/db?autoconnect=1',
        'mydb.admin' => 'mock://admin:pass@host/db?autoconnect=1'
      );
      $cm= $this->instanceWith($dsns);
      $this->assertEquals(new DSN($dsns['mydb.user']), $cm->get('mydb', 'user')->dsn);
    }
 
    /**
     * Acquire first connection by host
     *
     */
    #[@test]
    public function getFirstByHost() {
      $dsns= array(
        'mydb.user'  => 'mock://user:pass@host/db?autoconnect=1',
        'mydb.admin' => 'mock://admin:pass@host/db?autoconnect=1'
      );
      $cm= $this->instanceWith($dsns);
      $this->assertEquals(new DSN($dsns['mydb.user']), $cm->getByHost('mydb', 0)->dsn);
    }
 
    /**
     * Acquire first connection by host
     *
     */
    #[@test]
    public function getSecondByHost() {
      $dsns= array(
        'mydb.user'  => 'mock://user:pass@host/db?autoconnect=1',
        'mydb.admin' => 'mock://admin:pass@host/db?autoconnect=1'
      );
      $cm= $this->instanceWith($dsns);
      $this->assertEquals(new DSN($dsns['mydb.admin']), $cm->getByHost('mydb', 1)->dsn);
    }

    /**
     * Acquire all connections by host
     *
     */
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
        array(new DSN($dsns['mydb.user']), new DSN($dsns['mydb.admin'])), 
        $values
      );
    }
  }
?>
