<?php
/* This class is part of the XP framework
 *
 * $Id: DSNTest.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace net::xp_framework::unittest::rdbms;

  ::uses('unittest.TestCase', 'rdbms.DSN');

  /**
   * Tests the DSN class
   *
   * @see      xp://rdbms.DSN
   * @purpose  Unit Test
   */
  class DSNTest extends unittest::TestCase {
    public
      $dsn= array();
      
    /**
     * Setup method
     *
     */
    public function setUp() {
      $this->dsn['sybase']= new rdbms::DSN('sybase://sa:password@localhost:1999/CAFFEINE?autoconnect=1');
      $this->dsn['mysql']= new rdbms::DSN('mysql://root@localhost/?log=default');
      $this->dsn['pgsql']= new rdbms::DSN('pgsql://postgres:1433/db?observer[util.log.LogObserver]=default');
    }

    /**
     * Tests the toString() method returns passwords replaced by stars.
     *
     */
    #[@test]
    public function stringRepresentationWithPassword() {
      $this->assertEquals(
        'rdbms.DSN@(sybase://sa:********@localhost:1999/CAFFEINE?autoconnect=1)',
        $this->dsn['sybase']->toString()
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
        $this->dsn['mysql']->toString()
      );
    }
    
    /**
     * Tests the getFlags() method on a DSN without flags
     *
     */
    #[@test]
    public function noFlags() {
      $this->assertEquals(0, $this->dsn['mysql']->getFlags());
    }

    /**
     * Tests the getFlags() method on a DSN with flags
     *
     */
    #[@test]
    public function definedFlags() {
      $this->assertEquals(DB_AUTOCONNECT, $this->dsn['sybase']->getFlags());
    }
    
    /**
     * Tests the getProperty() method
     *
     */
    #[@test]
    public function stringPropertyValue() {
      $this->assertEquals('default', $this->dsn['mysql']->getProperty('log'));
    }

    /**
     * Tests the getProperty() method
     *
     */
    #[@test]
    public function arrayPropertyValue() {
      $this->assertEquals(array('util.log.LogObserver' => 'default'), $this->dsn['pgsql']->getProperty('observer'));
    }
  }
?>
