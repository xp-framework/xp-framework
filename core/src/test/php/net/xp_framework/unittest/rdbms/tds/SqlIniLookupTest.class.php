<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'rdbms.tds.SqlIniLookup'
  );

  /**
   * TestCase
   *
   * @see   xp://rdbms.tds.SqlIniLookup
   */
  class SqlIniLookupTest extends TestCase {
    protected $fixture= NULL;
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->fixture= new SqlIniLookup($this->getClass()->getPackage()->getResourceAsStream('sql.ini'));
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function tearDown() {
      // TODO: Fill code that gets executed after every test method
      //       or remove this method
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function lookup() {
      $dsn= new DSN('sybase://carla/tempdb');
      $this->fixture->lookup($dsn);
      $this->assertEquals(new DSN('sybase://carla.example.com:5000/tempdb'), $dsn);
    }
  }
?>
