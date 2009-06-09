<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'rdbms.DriverManager'
  );

  /**
   * TestCase
   *
   * @see      xp://rdbms.DriverManager
   * @purpose  purpose
   */
  class DriverManagerTest extends TestCase {
  
    /**
     * Test getConnection() throws an exception in case an unsupported
     * driver is encountered
     *
     */
    #[@test, @expect('rdbms.DriverNotSupportedException')]
    public function unsupported() {
      DriverManager::getConnection('unsupported://localhost');
    }

    /**
     * Test registering a connection
     *
     */
    #[@test]
    public function register() {
      DriverManager::register('mock', XPClass::forName('net.xp_framework.unittest.rdbms.mock.MockConnection'));
      $this->assertClass(
        DriverManager::getConnection('mock://localhost'),
        'net.xp_framework.unittest.rdbms.mock.MockConnection'
      );
    }

    /**
     * Test registering a class which is not a subclass of rdbms.DBConnection
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function registerNonDbConnection() {
      DriverManager::register('fail', $this->getClass());
    }
  }
?>
