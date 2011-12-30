<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'rdbms.DriverManager',
    'net.xp_framework.unittest.rdbms.mock.MockConnection'
  );

  /**
   * TestCase
   *
   * @see      xp://rdbms.DriverManager
   */
  class DriverManagerTest extends TestCase {
    protected $registered= array();
  
    /**
     * Registers driver and tracks registration.
     *
     * @param   string name
     * @param   lang.XPClass class
     */
    protected function register($name, $class) {
      DriverManager::register($name, $class);
      $this->registered[]= $name;
    }
    
    /**
     * Tears down test case - removes all drivers registered via register().
     *
     */
    public function tearDown() {
      foreach ($this->registered as $name) {
        DriverManager::remove($name);
      }
    }

    /**
     * Test getConnection() throws an exception in case an unsupported
     * driver is encountered
     *
     */
    #[@test, @expect('rdbms.DriverNotSupportedException')]
    public function unsupportedDriver() {
      DriverManager::getConnection('unsupported://localhost');
    }

    /**
     * Test getConnection()
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function nullConnection() {
      DriverManager::getConnection(NULL);
    }

    /**
     * Test getConnection()
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function emptyConnection() {
      DriverManager::getConnection('');
    }

    /**
     * Test getConnection()
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function malformedConnection() {
      DriverManager::getConnection('not.a.dsn');
    }

    /**
     * Ensure "mysqlx" is always supported - this is our userland implementation 
     * for MySQL connectivity as snap-in replacement so even if PHP comes without 
     * MySQL (!), the XP Framework supports it.
     *
     */
    #[@test]
    public function mysqlxProvidedByDefaultDrivers() {
      $this->assertInstanceOf(
        'rdbms.mysqlx.MySqlxConnection', 
        DriverManager::getConnection('mysql+x://localhost')
      );
    }

    /**
     * Ensure querying specifically for "mysql+unsupported" will raise an 
     * exception and not find the "mysql" family driver.
     *
     */
    #[@test, @expect('rdbms.DriverNotSupportedException')]
    public function unsupportedDriverInMySQLDriverFamily() {
      DriverManager::getConnection('mysql+unsupported://localhost');
    }

    /**
     * Test "mysql" is always supported, in the "worst" case through our mysqlx 
     * implementation (see above).
     *
     */
    #[@test]
    public function mysqlAlwaysSupported() {
      $this->assertInstanceOf(
        'rdbms.DBConnection', 
        DriverManager::getConnection('mysql://localhost')
      );
    }

    /**
     * Test registering a connection
     *
     */
    #[@test]
    public function registerConnection() {
      $this->register('mock', XPClass::forName('net.xp_framework.unittest.rdbms.mock.MockConnection'));
      $this->assertInstanceOf(
        'net.xp_framework.unittest.rdbms.mock.MockConnection',
        DriverManager::getConnection('mock://localhost')
      );
    }

    /**
     * Test registering a class which is not a subclass of rdbms.DBConnection
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function registerNonDbConnection() {
      $this->register('fail', $this->getClass());
    }

    /**
     * Test searching for a connection
     *
     */
    #[@test]
    public function searchImplementation() {

      // Should not be found
      $this->register('tests', XPClass::forName('net.xp_framework.unittest.rdbms.mock.MockConnection'));

      // Should choose the "a" implementation
      $this->register('test+a', ClassLoader::defineClass(
        'net.xp_framework.unittest.rdbms.mock.AMockConnection', 
        'net.xp_framework.unittest.rdbms.mock.MockConnection', 
        array(), 
        '{}'
      ));
      $this->register('test+b', ClassLoader::defineClass(
        'net.xp_framework.unittest.rdbms.mock.BMockConnection', 
        'net.xp_framework.unittest.rdbms.mock.MockConnection', 
        array(), 
        '{}'
      ));

      $this->assertInstanceOf(
        'net.xp_framework.unittest.rdbms.mock.AMockConnection', 
        DriverManager::getConnection('test://localhost')
      );
    }
  }
?>
