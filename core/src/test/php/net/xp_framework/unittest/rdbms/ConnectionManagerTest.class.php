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
  class ConnectionManagerTest extends TestCase {
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
     * Returns connection manager instance configured with the given 
     * properties. 
     *
     * @see     xp://rdbms.ConnectionManager::configure
     * @param   util.Properties prop
     * @return  rdbms.ConnectionManager
     */
    protected function configured(Properties $prop) {
      $cm= ConnectionManager::getInstance();
      $cm->configure($prop);
      return $cm;
    }
    
    /**
     * Check configure with an empty properties file yields an an empty
     * connection manager instance pool.
     *
     */
    #[@test]
    public function callingConfigureWithEmptyProperties() {
      $this->assertEquals(array(), $this->configured(Properties::fromString(''))->getConnections());
    }
    
    /**
     * Acquire an existing connection
     *
     */
    #[@test]
    public function acquireExistingConnectionViaGetByHost() {
      $cm= $this->configured(Properties::fromString('[mydb]
        dsn="mock://user:pass@host/db?autoconnect=1
      '));

      $this->assertInstanceOf(self::MOCK_CONNECTION_CLASS, $cm->getByHost('mydb', 0));
    }
    
    /**
     * Try to acquire a non-existant connection
     *
     */
    #[@test, @expect('rdbms.ConnectionNotRegisteredException')]
    public function acquireNonExistantConnectionViaGetByHost() {
      $cm= $this->configured(Properties::fromString('[mydb]
        dsn="mock://user:pass@host/db?autoconnect=1
      '));

      $cm->getByHost('yourdb', 0);
    }

    /**
     * Acquire an existing connection
     *
     */
    #[@test]
    public function acquireExistingConnectionViaGet() {
      $cm= $this->configured(Properties::fromString('[mydb]
        dsn="mock://user:pass@host/db?autoconnect=1
      '));

      $this->assertInstanceOf(self::MOCK_CONNECTION_CLASS, $cm->getByHost('mydb', 0));
    }
    
    /**
     * Try to acquire a non-existant connection
     *
     */
    #[@test, @expect('rdbms.ConnectionNotRegisteredException')]
    public function acquireNonExistantConnectionWithExistantUserViaGet() {
      $cm= $this->configured(Properties::fromString('[mydb]
        dsn="mock://user:pass@host/db?autoconnect=1
      '));

      $cm->get('nonexistant', 'user');
    }

    /**
     * Try to acquire a non-existant connection
     *
     */
    #[@test, @expect('rdbms.ConnectionNotRegisteredException')]
    public function acquireExistantConnectionWithNonExistantUserViaGet() {
      $cm= $this->configured(Properties::fromString('[mydb]
        dsn="mock://user:pass@host/db?autoconnect=1
      '));

      $cm->get('mydb', 'nonexistant');
    }
    
    /**
     * Check that configuring with a not supported scheme works.
     *
     */
    #[@test]
    public function configureInvalid() {
      $this->configured(Properties::fromString('[mydb]
        dsn="invalid://user:pass@host/db?autoconnect=1
      '));
    }
    
    /**
     * Acquiring an unsupported connection should throw a
     * rdbms.DriverNotSupportedException
     *
     */
    #[@test, @expect('rdbms.DriverNotSupportedException')]
    public function acquireInvalid() {
      $this->configured(Properties::fromString('[mydb]
        dsn="invalid://user:pass@host/db?autoconnect=1
      '))->getByHost('mydb', 0);
    }
  }
?>
