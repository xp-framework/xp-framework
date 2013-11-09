<?php namespace net\xp_framework\unittest\rdbms;

use rdbms\DriverManager;

/**
 * TestCase
 *
 * @see  xp://rdbms.DriverManager
 */
class DriverManagerTest extends \unittest\TestCase {
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
   */
  public function tearDown() {
    foreach ($this->registered as $name) {
      DriverManager::remove($name);
    }
  }

  #[@test, @expect('rdbms.DriverNotSupportedException')]
  public function unsupportedDriver() {
    DriverManager::getConnection('unsupported://localhost');
  }

  #[@test, @expect('lang.FormatException')]
  public function nullConnection() {
    DriverManager::getConnection(null);
  }

  #[@test, @expect('lang.FormatException')]
  public function emptyConnection() {
    DriverManager::getConnection('');
  }

  #[@test, @expect('lang.FormatException')]
  public function malformedConnection() {
    DriverManager::getConnection('not.a.dsn');
  }

  #[@test]
  public function mysqlxProvidedByDefaultDrivers() {
    $this->assertInstanceOf(
      'rdbms.mysqlx.MySqlxConnection', 
      DriverManager::getConnection('mysql+x://localhost')
    );
  }

  #[@test, @expect('rdbms.DriverNotSupportedException')]
  public function unsupportedDriverInMySQLDriverFamily() {
    DriverManager::getConnection('mysql+unsupported://localhost');
  }

  #[@test]
  public function mysqlAlwaysSupported() {
    $this->assertInstanceOf(
      'rdbms.DBConnection', 
      DriverManager::getConnection('mysql://localhost')
    );
  }

  #[@test]
  public function registerConnection() {
    $this->register('mock', \lang\XPClass::forName('net.xp_framework.unittest.rdbms.mock.MockConnection'));
    $this->assertInstanceOf(
      'net.xp_framework.unittest.rdbms.mock.MockConnection',
      DriverManager::getConnection('mock://localhost')
    );
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function registerNonDbConnection() {
    $this->register('fail', $this->getClass());
  }

  #[@test]
  public function searchImplementation() {

    // Should not be found
    $this->register('tests', \lang\XPClass::forName('net.xp_framework.unittest.rdbms.mock.MockConnection'));

    // Should choose the "a" implementation
    $this->register('test+a', \lang\ClassLoader::defineClass(
      'net.xp_framework.unittest.rdbms.mock.AMockConnection', 
      'net.xp_framework.unittest.rdbms.mock.MockConnection', 
      array(), 
      '{}'
    ));
    $this->register('test+b', \lang\ClassLoader::defineClass(
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
