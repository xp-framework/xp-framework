<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.profiling.unittest.TestCase', 'rdbms.DSN');

  /**
   * Tests the DSN class
   *
   * @see      xp://rdbms.DSN
   * @purpose  Unit Test
   */
  class DSNTest extends TestCase {
    public
      $dsn= array();
      
    /**
     * Setup method
     *
     * @access  public
     */
    public function setUp() {
      $this->dsn['sybase']= &new DSN('sybase://sa:password@localhost:1999/CAFFEINE?autoconnect=1');
      $this->dsn['mysql']= &new DSN('mysql://root@localhost/?log=default');
      $this->dsn['pgsql']= &new DSN('pgsql://postgres:1433/db?observer[util.log.LogObserver]=default');
    }

    /**
     * Tests the toString() method returns passwords replaced by stars.
     *
     * @access  public
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
     * @access  public
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
     * @access  public
     */
    #[@test]
    public function noFlags() {
      $this->assertEquals(0, $this->dsn['mysql']->getFlags());
    }

    /**
     * Tests the getFlags() method on a DSN with flags
     *
     * @access  public
     */
    #[@test]
    public function definedFlags() {
      $this->assertEquals(DB_AUTOCONNECT, $this->dsn['sybase']->getFlags());
    }
    
    /**
     * Tests the getProperty() method
     *
     * @access  public
     */
    #[@test]
    public function stringPropertyValue() {
      $this->assertEquals('default', $this->dsn['mysql']->getProperty('log'));
    }

    /**
     * Tests the getProperty() method
     *
     * @access  public
     */
    #[@test]
    public function arrayPropertyValue() {
      $this->assertEquals(array('util.log.LogObserver' => 'default'), $this->dsn['pgsql']->getProperty('observer'));
    }
  }
?>
