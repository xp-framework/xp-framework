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
    var
      $dsn= array();
      
    /**
     * Setup method
     *
     * @access  public
     */
    function setUp() {
      $this->dsn['sybase']= &new DSN('sybase://sa:password@localhost:1999/CAFFEINE?autoconnect=1');
      $this->dsn['mysql']= &new DSN('mysql://root@localhost/');
    }

    /**
     * Tests the toString() method returns passwords replaced by stars.
     *
     * @access  public
     */
    #[@test]
    function stringRepresentationWithPassword() {
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
    function stringRepresentationWithoutPassword() {
      $this->assertEquals(
        'rdbms.DSN@(mysql://root@localhost/)',
        $this->dsn['mysql']->toString()
      );
    }
  }
?>
