<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase', 'rdbms.DSN');

  /**
   * Tests the DSN class
   *
   * @see      xp://rdbms.DSN
   * @purpose  Unit Test
   */
  class DSNTest extends TestCase {

    /**
     * Tests the toString() method returns passwords replaced by stars.
     *
     */
    #[@test]
    public function stringRepresentationWithPassword() {
      $this->assertEquals(
        'rdbms.DSN@(sybase://sa:********@localhost:1999/CAFFEINE?autoconnect=1)',
        create(new DSN('sybase://sa:password@localhost:1999/CAFFEINE?autoconnect=1'))->toString()
      );
    }
    
    /**
     * Tests the toString() method does not mangle DSNs without passwords
     *
     */
    #[@test]
    public function stringRepresentationWithoutPassword() {
      $this->assertEquals(
        'rdbms.DSN@(mysql://root@localhost/?log=default)',
        create(new DSN('mysql://root@localhost/?log=default'))->toString()
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function asStringRemovesPassword() {
      $this->assertEquals(
        'mysql://user:********@localhost/?log=default',
        create(new DSN('mysql://user:foobar@localhost/?log=default'))->asString()
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function asStringKeepsPasswordIfRequested() {
      $this->assertEquals(
        'mysql://user:foobar@localhost/?log=default',
        create(new DSN('mysql://user:foobar@localhost/?log=default'))->asString(TRUE)
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function asStringSkipsUserEvenWithRaw() {
      $this->assertEquals(
        'mysql://localhost/?log=default',
        create(new DSN('mysql://localhost/?log=default'))->asString(TRUE)
      );
    }

    /**
     * Tests the getDriver() method
     *
     */
    #[@test]
    public function driver() {
      $this->assertEquals(
        'sybase', 
        create(new DSN('sybase://TEST/'))->getDriver()
      );
    }

    /**
     * Tests the getDriver() method
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function noDriver() {
      new DSN('');
    }

    /**
     * Tests the getHost() method
     *
     */
    #[@test]
    public function host() {
      $this->assertEquals(
        'TEST', 
        create(new DSN('sybase://TEST/'))->getHost()
      );
    }
    
    /**
     * Tests the getPort() method
     *
     */
    #[@test]
    public function port() {
      $this->assertEquals(
        1999, 
        create(new DSN('sybase://TEST:1999/'))->getPort()
      );
    }

    /**
     * Tests the getPort() method
     *
     */
    #[@test]
    public function portDefault() {
      $this->assertEquals(
        1999, 
        create(new DSN('sybase://TEST:1999/'))->getPort(5000)
      );
    }

    /**
     * Tests the getPort() method
     *
     */
    #[@test]
    public function noPort() {
      $this->assertNull(create(new DSN('sybase://TEST/'))->getPort());
    }

    /**
     * Tests the getPort() method
     *
     */
    #[@test]
    public function noPortDefault() {
      $this->assertEquals(
        1999, 
        create(new DSN('sybase://TEST/'))->getPort(1999)
      );
    }

    /**
     * Tests the getDatabase() method
     *
     */
    #[@test]
    public function database() {
      $this->assertEquals(
        'CAFFEINE', 
        create(new DSN('sybase://TEST/CAFFEINE'))->getDatabase()
      );
    }

    /**
     * Tests the getDatabase() method
     *
     */
    #[@test]
    public function databaseDefault() {
      $this->assertEquals(
        'CAFFEINE', 
        create(new DSN('sybase://TEST/CAFFEINE'))->getDatabase('master')
      );
    }

    /**
     * Tests the getDatabase() method
     *
     */
    #[@test]
    public function noDatabase() {
      $this->assertNull(create(new DSN('mysql://root@localhost'))->getDatabase());
    }

    /**
     * Tests the getDatabase() method
     *
     */
    #[@test]
    public function noDatabaseDefault() {
      $this->assertEquals(
        'master', 
        create(new DSN('mysql://root@localhost'))->getDatabase('master')
      );
    }

    /**
     * Tests the getDatabase() method
     *
     */
    #[@test]
    public function slashDatabase() {
      $this->assertNull(create(new DSN('mysql://root@localhost/'))->getDatabase());
    }

    /**
     * Tests the getDatabase() method
     *
     */
    #[@test]
    public function slashDatabaseDefault() {
      $this->assertEquals(
        'master', 
        create(new DSN('mysql://root@localhost/'))->getDatabase('master')
      );
    }

    /**
     * Tests the getDatabase() method
     *
     */
    #[@test]
    public function fileDatabase() {
      $this->assertEquals(
        '/usr/local/fb/jobs.fdb', 
        create(new DSN('ibase://localhost//usr/local/fb/jobs.fdb'))->getDatabase()
      );
    }

    /**
     * Tests the getUser() method
     *
     */
    #[@test]
    public function user() {
      $this->assertEquals(
        'sa', 
        create(new DSN('sybase://sa@TEST'))->getUser()
      );
    }

    /**
     * Tests the getUser() method
     *
     */
    #[@test]
    public function userDefault() {
      $this->assertEquals(
        'sa', 
        create(new DSN('sybase://sa@TEST'))->getUser('reader')
      );
    }

    /**
     * Tests the getUser() method
     *
     */
    #[@test]
    public function noUser() {
      $this->assertNull(create(new DSN('sybase://TEST'))->getUser());
    }

    /**
     * Tests the getUser() method
     *
     */
    #[@test]
    public function noUserDefault() {
      $this->assertEquals(
        'reader', 
        create(new DSN('sybase://TEST'))->getUser('reader')
      );
    }

    /**
     * Tests the getPassword() method
     *
     */
    #[@test]
    public function password() {
      $this->assertEquals(
        'password', 
        create(new DSN('sybase://sa:password@TEST'))->getPassword()
      );
    }

    /**
     * Tests the getPassword() method
     *
     */
    #[@test]
    public function passwordDefault() {
      $this->assertEquals(
        'password', 
        create(new DSN('sybase://sa:password@TEST'))->getPassword('secret')
      );
    }

    /**
     * Tests the getPassword() method
     *
     */
    #[@test]
    public function noPassword() {
      $this->assertNull(create(new DSN('sybase://sa@TEST'))->getPassword());
    }

    /**
     * Tests the getPassword() method
     *
     */
    #[@test]
    public function noPasswordDefault() {
      $this->assertEquals(
        'secret', 
        create(new DSN('sybase://sa@TEST'))->getPassword('secret')
      );
    }
    
    /**
     * Tests the getFlags() method on a DSN without flags
     *
     */
    #[@test]
    public function noFlags() {
      $this->assertEquals(0, create(new DSN('sybase://sa@TEST'))->getFlags());
    }

    /**
     * Tests the getFlags() method on a DSN with flags
     *
     */
    #[@test]
    public function definedFlags() {
      $this->assertEquals(
        DB_AUTOCONNECT, 
        create(new DSN('sybase://sa@TEST?autoconnect=1'))->getFlags()
      );
    }
    
    /**
     * Tests the getProperty() method
     *
     */
    #[@test]
    public function stringPropertyValue() {
      $this->assertEquals(
        'default', 
        create(new DSN('sybase://sa@TEST?log=default'))->getProperty('log')
      );
    }

    /**
     * Tests the getProperty() method
     *
     */
    #[@test]
    public function arrayPropertyValue() {
      $this->assertEquals(
        array('util.log.LogObserver' => 'default'), 
        create(new DSN('pgsql://postgres:1433/db?observer[util.log.LogObserver]=default'))->getProperty('observer')
      );
    }

    /**
     * Tests the equals() method
     *
     */
    #[@test]
    public function twoDsnsCreatedFromSameStringAreEqual() {
      $string= 'scheme://user:password@host/DATABASE?log=default&autoconnect=1';
      $this->assertEquals(new DSN($string), new DSN($string));
    }

    /**
     * Tests the equals() method
     *
     */
    #[@test]
    public function twoDsnsWithDifferingAutoconnectNotEqual() {
      $this->assertNotEquals(
        new DSN('scheme://user:password@host/DATABASE?log=default&autoconnect=0'), 
        new DSN('scheme://user:password@host/DATABASE?log=default&autoconnect=1')
      );
    }

    /**
     * Tests the equals() method
     *
     */
    #[@test]
    public function twoDsnsWithDifferingParamsNotEqual() {
      $this->assertNotEquals(
        new DSN('scheme://user:password@host/DATABASE'), 
        new DSN('scheme://user:password@host/DATABASE?log=default')
      );
    }

    /**
     * Tests the equals() method
     *
     */
    #[@test]
    public function twoDsnsWithDifferingFlagParamsNotEqual() {
      $this->assertNotEquals(
        new DSN('scheme://user:password@host/DATABASE'), 
        new DSN('scheme://user:password@host/DATABASE?autoconnect=1')
      );
    }

    /**
     * Tests the equals() method
     *
     */
    #[@test]
    public function twoDsnsWithDifferingObserverParamsNotEqual() {
      $this->assertNotEquals(
        new DSN('scheme://user:password@host/DATABASE?observer[rdbms.sybase.SybaseShowplanObserver]=sql'), 
        new DSN('scheme://user:password@host/DATABASE?observer[util.log.LogObserver]=default')
      );
    }

    /**
     * Tests the equals() method
     *
     */
    #[@test]
    public function twoDsnsWithDifferingObserverParamValuesNotEqual() {
      $this->assertNotEquals(
        new DSN('scheme://user:password@host/DATABASE?observer[util.log.LogObserver]=sql'), 
        new DSN('scheme://user:password@host/DATABASE?observer[util.log.LogObserver]=default')
      );
    }

    /**
     * Tests the equals() method
     *
     */
    #[@test]
    public function twoDsnsWithSameObserverParamsEqual() {
      $this->assertEquals(
        new DSN('scheme://user:password@host/DATABASE?observer[util.log.LogObserver]=default'), 
        new DSN('scheme://user:password@host/DATABASE?observer[util.log.LogObserver]=default')
      );
    }

    /**
     * Tests the equals() method
     *
     */
    #[@test]
    public function twoDsnsWithDifferentlyOrderedParamsAreEqual() {
      $this->assertEquals(
        new DSN('scheme://host/DATABASE?autoconnect=1&observer[rdbms.sybase.SybaseShowplanObserver]=sql&log=default'), 
        new DSN('scheme://host/DATABASE?log=default&observer[rdbms.sybase.SybaseShowplanObserver]=sql&autoconnect=1')
      );
    }
  }
?>
