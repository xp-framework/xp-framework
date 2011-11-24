<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.Observer',
    'unittest.TestCase',
    'rdbms.DriverManager'
  );

  /**
   * Base class for Rdbms tests
   *
   */
  abstract class RdbmsIntegrationTest extends TestCase {
    protected $dsn= NULL;
    protected $conn= NULL;

    /**
     * Set up testcase
     *
     */
    public function setUp() {
      $this->dsn= Properties::fromString($this->getClass()->getPackage()->getResource('database.ini'))->readString(
        $this->_dsn(),
        'dsn',
        NULL
      );

      if (NULL === $this->dsn) {
        throw new PrerequisitesNotMetError('No credentials for '.$this->getClassName());
      }

      try {
        $this->conn= DriverManager::getConnection($this->dsn);
      } catch (Throwable $t) {
        throw new PrerequisitesNotMetError($t->getMessage(), $t);
      }
    }

    /**
     * Tear down test case, close connection.
     *
     */
    public function tearDown() {
      $this->conn->close();
    }

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
      $connect && $this->conn->connect();
      return $this->conn;
    }
    
    /**
     * Test query throws rdbms.SQLStateException when not connected
     * to the database
     *
     */
    #[@test, @expect('rdbms.SQLStateException')]
    public function noQueryWhenNotConnected() {
      $this->conn->query('select 1');
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
      $this->assertEquals(TRUE, $this->conn->connect());
    }

    /**
     * Test query throws rdbms.SQLStateException when no longer 
     * connected to the database
     *
     */
    #[@test, @expect('rdbms.SQLStateException')]
    public function noQueryWhenDisConnected() {
      $this->conn->connect();
      $this->conn->close();
      $this->conn->query('select 1');
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
     * Test query() and next()
     *
     */
    #[@test]
    public function queryAndNext() {
      $q= $this->db()->query('select 1 as foo');
      $this->assertInstanceOf('rdbms.ResultSet', $q);
      $this->assertEquals(array('foo' => 1), $q->next());
    }
 
    /**
     * Test query() and next()
     *
     */
    #[@test]
    public function queryAndNextWithField() {
      $q= $this->db()->query('select 1 as foo');
      $this->assertInstanceOf('rdbms.ResultSet', $q);
      $this->assertEquals(1, $q->next('foo'));
    }

    /**
     * Test open() and next()
     *
     */
    #[@test]
    public function openAndNext() {
      $q= $this->db()->open('select 1 as foo');
      $this->assertInstanceOf('rdbms.ResultSet', $q);
      $this->assertEquals(array('foo' => 1), $q->next());
    }

    /**
     * Test open() and next()
     *
     */
    #[@test]
    public function openAndNextWithField() {
      $q= $this->db()->open('select 1 as foo');
      $this->assertInstanceOf('rdbms.ResultSet', $q);
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
      $this->assertInstanceOf('rdbms.ResultSet', $q);
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
     * Create autoincrement table
     *
     */
    protected function createTable() {
      $this->removeTable('unittest');
      $this->db()->query('create table unittest (pk int, username varchar(30))');
      $this->db()->insert('into unittest values (1, "kiesel")');
      $this->db()->insert('into unittest values (2, "kiesel")');
    }

    /**
     * Helper method to create table
     *
     * @param   string name
     */
    protected function createAutoIncrementTable($name) {
      raise('lang.MethodNotImplementedException', __FUNCTION__);
    }

    /**
     * Create transactions table
     *
     * @param   string name
     */
    protected function createTransactionsTable($name) {
      raise('lang.MethodNotImplementedException', __FUNCTION__);
    }
    
    /**
     * Test insert via query()
     *
     */
    #[@test]
    public function insertViaQuery() {
      $this->createTable();
      $this->assertTrue($this->db()->query('insert into unittest values (1, "kiesel")'));
    }

    /**
     * Test insert()
     *
     */
    #[@test]
    public function insertIntoTable() {
      $this->createTable();
      $this->assertEquals(1, $this->db()->insert('into unittest values (2, "xp")'));
    }

    /**
     * Test update via query()
     *
     */
    #[@test]
    public function updateViaQuery() {
      $this->createTable();
      $this->assertTrue($this->db()->query('update unittest set pk= pk+ 1 where pk= 2'));
    }
    
    /**
     * Test update()
     *
     */
    #[@test]
    public function updateTable() {
      $this->createTable();
      $this->assertEquals(1, $this->db()->update('unittest set pk= pk+ 1 where pk= 1'));
    }

    /**
     * Test delete via query()
     *
     */
    #[@test]
    public function deleteViaQuery() {
      $this->createTable();
      $this->assertTrue($this->db()->query('delete from unittest where pk= 2'));
    }
    
    /**
     * Test delete()
     *
     */
    #[@test]
    public function deleteFromTable() {
      $this->createTable();
      $this->assertEquals(1, $this->db()->delete('from unittest where pk= 1'));
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
     * Test selecting NULL
     *
     */
    #[@test]
    public function selectNull() {
      $this->assertEquals(NULL, $this->db()->query('select NULL as value')->next('value'));
    }
    
    /**
     * Test selecting integer values
     *
     */
    #[@test]
    public function selectInteger() {
      $this->assertEquals(1, $this->db()->query('select 1 as value')->next('value'));
    }

    /**
     * Test selecting integer values
     *
     */
    #[@test]
    public function selectIntegerZero() {
      $this->assertEquals(0, $this->db()->query('select 0 as value')->next('value'));
    }

    /**
     * Test selecting integer values
     *
     */
    #[@test]
    public function selectNegativeInteger() {
      $this->assertEquals(-6100, $this->db()->query('select -6100 as value')->next('value'));
    }
    
    /**
     * Test selecting string values
     *
     */
    #[@test]
    public function selectString() {
      $this->assertEquals('Hello, World!', $this->db()->query('select "Hello, World!" as value')->next('value'));
    }

    /**
     * Test selecting string values with an umlaut inside
     *
     */
    #[@test]
    public function selectUmlautString() {
      $this->assertEquals('Übercoder', $this->db()->query('select %s as value', 'Übercoder')->next('value'));
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
     * Test selecting float values
     *
     */
    #[@test]
    public function selectFloatOne() {
      $this->assertEquals(1.0, $this->db()->query('select 1.0 as value')->next('value'));
    }

    /**
     * Test selecting float values
     *
     */
    #[@test]
    public function selectFloatZero() {
      $this->assertEquals(0.0, $this->db()->query('select 0.0 as value')->next('value'));
    }

    /**
     * Test selecting float values
     *
     */
    #[@test]
    public function selectNegativeFloat() {
      $this->assertEquals(-6.1, $this->db()->query('select -6.1 as value')->next('value'));
    }
    
    /**
     * Test selecting date values returns util.Date objects
     *
     */
    #[@test]
    public function selectDate() {
      $cmp= new Date('2009-08-14 12:45:00');
      $result= $this->db()->query('select cast(%s as date) as value', $cmp)->next('value');
      
      $this->assertInstanceOf('util.Date', $result);
      $this->assertEquals($cmp->toString('Y-m-d'), $result->toString('Y-m-d'));
    }
    
    /**
     * Test observers are being called
     *
     */
    #[@test]
    public function observe() {
      $observer= newinstance('util.Observer', array(), '{
        protected $observations= array();
        
        public function numberOfObservations() {
          return sizeof($this->observations);
        }
        
        public function observationAt($i) {
          return $this->observations[$i]["arg"];
        }
        
        public function update($obs, $arg= NULL) {
          $this->observations[]= array("observable" => $obs, "arg" => $arg);
        }
      }');
      
      $db= $this->db();
      $db->addObserver($observer);
      $db->query('select 1');
      
      $this->assertEquals(2, $observer->numberOfObservations());
      
      with ($o0= $observer->observationAt(0)); {
        $this->assertInstanceOf('rdbms.DBEvent', $o0);
        $this->assertEquals('query', $o0->getName());
        $this->assertEquals('select 1', $o0->getArgument());
      }

      with ($o1= $observer->observationAt(1)); {
        $this->assertInstanceOf('rdbms.DBEvent', $o1);
        $this->assertEquals('queryend', $o1->getName());
        $this->assertInstanceOf('rdbms.ResultSet', $o1->getArgument());
      }
    }

    /**
     * Test transactions
     *
     */
    #[@test]
    public function rolledBackTransaction() {
      $this->createTransactionsTable('unittest');
      $db= $this->db();

      $tran= $db->begin(new Transaction('test'));
      $db->insert('into unittest values (1, "should_not_be_here")');
      $tran->rollback();
      
      $this->assertEquals(array(), $db->select('* from unittest'));
    }


    /**
     * Test transactions
     *
     */
    #[@test]
    public function committedTransaction() {
      $this->createTransactionsTable('unittest');
      $db= $this->db();

      $tran= $db->begin(new Transaction('test'));
      $db->insert('into unittest values (1, "should_be_here")');
      $tran->commit();
      
      $this->assertEquals(array(array('pk' => 1, 'username' => 'should_be_here')), $db->select('* from unittest'));
    }

    /**
     * Test not reading until the end of a non-buffered result
     *
     */
    #[@test]
    public function unbufferedReadNoResults() {
      $this->createTable();
      $db= $this->db();

      $db->open('select * from unittest');

      $this->assertEquals(1, $db->query('select 1 as num')->next('num'));
    }
    
    /**
     * Test not reading until the end of a non-buffered result
     *
     */
    #[@test]
    public function unbufferedReadOneResult() {
      $this->createTable();
      $db= $this->db();

      $q= $db->open('select * from unittest');
      $this->assertEquals(array('pk' => 1, 'username' => 'kiesel'), $q->next());

      $this->assertEquals(1, $db->query('select 1 as num')->next('num'));
    }
  }
?>
