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
   * Base class for Rdbms tests
   *
   */
  abstract class RdbmsIntegrationTest extends TestCase {

    /**
     * Retrieve dsn
     *
     * @return  string
     */
    abstract public function _dsn();
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      // TODO: Fill code that gets executed before every test method
      //       or remove this method
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
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    protected function db() {
      with ($db= DriverManager::getConnection($this->_dsn())); {
        $db->connect();
      }
      
      return $db;
    }    
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@test]
    public function connect() {
      $this->db()->close();
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function simpleStaticQuery() {
      $this->assertEquals(
        array(array('foo' => 1)), 
        $this->db()->select('1 as foo')
      );
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function simpleQuery() {
      $q= $this->db()->query('select 1 as foo');
      $this->assertSubclass($q, 'rdbms.ResultSet');
      $this->assertEquals(1, $q->next('foo'));
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    protected function removeTable($name) {
      // Try to remove, if already exist...
      try {
        $this->db()->query('drop table %c', $name);
      } catch (SQLStatementFailedException $ignored) {}
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    protected function createTable() {
      $this->removeTable('unittest');
      $this->db()->query('create table unittest (pk int, username varchar(30))');
      $this->db()->insert('into unittest values (1, "kiesel")');
      $this->db()->insert('into unittest values (2, "kiesel")');
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function insertIntoTable() {
      $this->createTable();
      $q= $this->db()->query('insert into unittest values (1, "kiesel")');
      $this->assertEquals(TRUE, $q);
      
      $q= $this->db()->insert('into unittest values (2, "xp")');
      $this->assertEquals(1, $q);
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function updateTable() {
      $this->createTable();
      $this->assertEquals(
        TRUE,
        $this->db()->query('update unittest set pk= pk+ 1 where pk= 2')
      );

      $this->assertEquals(
        1, 
        $this->db()->update('unittest set pk= pk+ 1 where pk= 1')
      );
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function deleteFromTable() {
      $this->createTable();
      $this->assertEquals(
        TRUE,
        $this->db()->query('delete from unittest where pk= 2')
      );

      $this->assertEquals(
        1, 
        $this->db()->delete('from unittest where pk= 1')
      );
    }
    
    /**
     * Test
     *
     */
    protected function _identity($name) {
      return $this->db()->identity();
    }
    
    
    /**
     * Test
     *
     */
    #[@test]
    public function identity() {
      $this->createAutoIncrementTable('unittest_ai');      
      $this->assertEquals(1, $this->db()->insert('into unittest_ai (username) values ("kiesel")'));
      $this->assertEquals(1, $this->_identity('unittest_ai'));
    }
  }
?>
