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
    public function noPort() {
      $this->assertNull(create(new DSN('sybase://TEST/'))->getPort());
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
    public function noDatabase() {
      $this->assertNull(create(new DSN('mysql://root@localhost'))->getDatabase());
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
    public function noUser() {
      $this->assertNull(create(new DSN('sybase://TEST'))->getUser());
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
    public function noPassword() {
      $this->assertNull(create(new DSN('sybase://sa@TEST'))->getPassword());
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
  }
?>
