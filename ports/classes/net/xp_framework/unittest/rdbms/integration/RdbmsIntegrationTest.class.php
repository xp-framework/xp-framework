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
    protected
      $dsn    = NULL;

    /**
     * Retrieve dsn section
     *
     * @return  string
     */
    abstract public function _dsn();

    /**
     * Retrieve database connection object
     *
     * @param   bool connect default TRUE
     * @return  rdbms.DBConnection
     */
    protected function db($connect= TRUE) {
      if (NULL === $this->dsn) {
        $this->dsn= Properties::fromString($this->getClass()->getPackage()->getResource('database.ini'))->readString(
          $this->_dsn(),
          'dsn',
          'dummy://user:pass@server/tempdb'
        );
      }
      
      with ($db= DriverManager::getConnection($this->dsn)); {
        if ($connect) $db->connect();
        return $db;
      }
    }
    
    /**
     * Test query throws rdbms.SQLStateException when not connected
     * to the database
     *
     */
    #[@test, @expect('rdbms.SQLStateException')]
    public function noQueryWhenNotConnected() {
      $db= $this->db(FALSE);
      $db->query('select 1');
    }
    
    /**
     * Test failing to connect throws rdbms.SQLConnectException
     *
     */
    #[@test, @expect('rdbms.SQLConnectException')]
    public function connectFailedThrowsException() {
      DriverManager::getConnection(str_replace(
        ':'.$this->db(FALSE)->dsn->getPassword().'@', 
        ':hopefully-wrong-password@', 
        $this->dsn
      ))->connect();
    }
    
    /**
     * Test connect()
     *
     */
    #[@test]
    public function connect() {
      $this->assertEquals(TRUE, $this->db(FALSE)->connect());
    }
    
    /**
     * Test select()
     *
     */
    #[@test]
    public function simpleSelect() {
      $this->assertEquals(
        array(array('foo' => 1)), 
        $this->db()->select('1 as foo')
      );
    }
    
    /**
     * Test query()
     *
     */
    #[@test]
    public function simpleQuery() {
      $q= $this->db()->query('select 1 as foo');
      $this->assertSubclass($q, 'rdbms.ResultSet');
      $this->assertEquals(1, $q->next('foo'));
    }
    
    /**
     * Test query() w/ an empty result set (empty or not - it should
     * be a ResultSet)
     *
     */
    #[@test]
    public function emptyQuery() {
      $this->createTable();
      $q= $this->db()->query('select * from unittest where 1=0');
      $this->assertSubclass($q, 'rdbms.ResultSet');
      $this->assertEquals(FALSE, $q->next());
    }
    
    /**
     * Helper method to remove table if existant
     *
     * @param   string name
     */
    protected function removeTable($name) {
      // Try to remove, if already exist...
      try {
        $this->db()->query('drop table %c', $name);
      } catch (SQLStatementFailedException $ignored) {}
    }
    
    /**
     * Helper method to create table
     *
     */
    protected function createTable() {
      $this->removeTable('unittest');
      $this->db()->query('create table unittest (pk int, username varchar(30))');
      $this->db()->insert('into unittest values (1, "kiesel")');
      $this->db()->insert('into unittest values (2, "kiesel")');
    }
    
    /**
     * Test insert()
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
     * Test update()
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
     * Test delete()
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
     * Test identity value retrieval through identity()
     *
     */
    #[@test]
    public function identity() {
      $this->createAutoIncrementTable('unittest_ai');      
      $this->assertEquals(1, $this->db()->insert('into unittest_ai (username) values ("kiesel")'));
      $first= $this->db()->identity('unittest_ai_pk_seq');
      
      $this->assertEquals(1, $this->db()->insert('into unittest_ai (username) values ("kiesel")'));
      $this->assertEquals($first+ 1, $this->db()->identity('unittest_ai_pk_seq'));
    }
    
    /**
     * Test failed query throws rdbms.SQLStatementFailedException
     *
     */
    #[@test, @expect('rdbms.SQLStatementFailedException')]
    public function malformedStatement() {
      $this->db()->query('select insert into delete.');
    }
    
    /**
     * Test selecting integer values leads 
     *
     */
    #[@test]
    public function selectInteger() {
      $this->assertEquals(1, $this->db()->query('select 1 as value')->next('value'));
    }
    
    /**
     * Test selecting string values
     *
     */
    #[@test]
    public function selectString() {
      $this->assertEquals("Hello, World!", $this->db()->query('select "Hello, World!" as value')->next('value'));
    }
    
    /**
     * Test selecting float values
     *
     */
    #[@test]
    public function selectFloat() {
      $this->assertEquals(0.5, $this->db()->query('select 0.5 as value')->next('value'));
    }
    
    /**
     * Test selecting date values returns util.Date objects
     *
     */
    #[@test]
    public function selectDate() {
      $cmp= new Date('2009-08-14 12:45:00');
      $result= $this->db()->query('select cast(%s as date) as value', $cmp)->next('value');
      
      $this->assertSubclass($result, 'util.Date');
      $this->assertEquals($cmp->toString('Y-m-d'), $result->toString('Y-m-d'));
    }
  }
?>
