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
      $q= $this->db()->query('select * from %c where 1 = 0', $this->tableName());
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
     * Creates table name
     *
     * @return  string
     */
    protected function tableName() {
      return 'unittest';
    }

    /**
     * Create autoincrement table
     *
     */
    protected function createTable() {
      $this->removeTable($this->tableName());
      $this->db()->query('create table %c (pk int, username varchar(30))', $this->tableName());
      $this->db()->insert('into %c values (1, "kiesel")', $this->tableName());
      $this->db()->insert('into %c values (2, "kiesel")', $this->tableName());
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
      $this->assertTrue($this->db()->query('insert into %c values (1, "kiesel")', $this->tableName()));
    }

    /**
     * Test insert()
     *
     */
    #[@test]
    public function insertIntoTable() {
      $this->createTable();
      $this->assertEquals(1, $this->db()->insert('into %c values (2, "xp")', $this->tableName()));
    }

    /**
     * Test update via query()
     *
     */
    #[@test]
    public function updateViaQuery() {
      $this->createTable();
      $this->assertTrue($this->db()->query('update %c set pk= pk+ 1 where pk= 2', $this->tableName()));
    }
    
    /**
     * Test update()
     *
     */
    #[@test]
    public function updateTable() {
      $this->createTable();
      $this->assertEquals(1, $this->db()->update('%c set pk= pk+ 1 where pk= 1', $this->tableName()));
    }

    /**
     * Test delete via query()
     *
     */
    #[@test]
    public function deleteViaQuery() {
      $this->createTable();
      $this->assertTrue($this->db()->query('delete from %c where pk= 2', $this->tableName()));
    }
    
    /**
     * Test delete()
     *
     */
    #[@test]
    public function deleteFromTable() {
      $this->createTable();
      $this->assertEquals(1, $this->db()->delete('from %c where pk= 1', $this->tableName()));
    }
    
    /**
     * Test identity value retrieval through identity()
     *
     */
    #[@test]
    public function identity() {
      $this->createAutoIncrementTable($this->tableName());      
      $this->assertEquals(1, $this->db()->insert('into %c (username) values ("kiesel")', $this->tableName()));
      $first= $this->db()->identity('unittest_pk_seq');
      
      $this->assertEquals(1, $this->db()->insert('into %c (username) values ("kiesel")', $this->tableName()));
      $this->assertEquals($first+ 1, $this->db()->identity('unittest_pk_seq'));
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
     * Test selecting string values
     *
     */
    #[@test]
    public function selectEmptyString() {
      $this->assertEquals('', $this->db()->query('select "" as value')->next('value'));
    }

    /**
     * Test selecting string values
     *
     */
    #[@test]
    public function selectSpace() {
      $this->assertEquals(' ', $this->db()->query('select " " as value')->next('value'));
    }

    /**
     * Test selecting string values with an umlaut inside
     *
     */
    #[@test]
    public function selectUmlautString() {
      $this->assertEquals('�bercoder', $this->db()->query('select %s as value', '�bercoder')->next('value'));
    }
    
    /**
     * Test selecting Decimal values
     *
     */
    #[@test]
    public function selectDecimalLiteral() {
      $this->assertEquals(0.5, $this->db()->query('select 0.5 as value')->next('value'));
    }

    /**
     * Test selecting Decimal values
     *
     */
    #[@test]
    public function selectDecimalLiteralOne() {
      $this->assertEquals(1.0, $this->db()->query('select 1.0 as value')->next('value'));
    }

    /**
     * Test selecting Decimal values
     *
     */
    #[@test]
    public function selectDecimalLiteralZero() {
      $this->assertEquals(0.0, $this->db()->query('select 0.0 as value')->next('value'));
    }

    /**
     * Test selecting Decimal values
     *
     */
    #[@test]
    public function selectNegativeDecimalLiteral() {
      $this->assertEquals(-6.1, $this->db()->query('select -6.1 as value')->next('value'));
    }
    
    /**
     * Test selecting Float values
     *
     */
    #[@test]
    public function selectFloat() {
      $this->assertEquals(0.5, $this->db()->query('select cast(0.5 as float) as value')->next('value'));
    }

    /**
     * Test selecting Float values
     *
     */
    #[@test]
    public function selectFloatOne() {
      $this->assertEquals(1.0, $this->db()->query('select cast(1.0 as float) as value')->next('value'));
    }

    /**
     * Test selecting Float values
     *
     */
    #[@test]
    public function selectFloatZero() {
      $this->assertEquals(0.0, $this->db()->query('select cast(0.0 as float) as value')->next('value'));
    }

    /**
     * Test selecting Float values
     *
     */
    #[@test]
    public function selectNegativeFloat() {
      $this->assertEquals(-6.1, round($this->db()->query('select cast(-6.1 as float) as value')->next('value'), 1));
    }

    /**
     * Test selecting Real values
     *
     */
    #[@test]
    public function selectReal() {
      $this->assertEquals(0.5, $this->db()->query('select cast(0.5 as real) as value')->next('value'));
    }

    /**
     * Test selecting Real values
     *
     */
    #[@test]
    public function selectRealOne() {
      $this->assertEquals(1.0, $this->db()->query('select cast(1.0 as real) as value')->next('value'));
    }

    /**
     * Test selecting Real values
     *
     */
    #[@test]
    public function selectRealZero() {
      $this->assertEquals(0.0, $this->db()->query('select cast(0.0 as real) as value')->next('value'));
    }

    /**
     * Test selecting Real values
     *
     */
    #[@test]
    public function selectNegativeReal() {
      $this->assertEquals(-6.1, round($this->db()->query('select cast(-6.1 as real) as value')->next('value'), 1));
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
     * Test selecting numeric values
     *
     */
    #[@test]
    public function selectNumericNull() {
      $this->assertEquals(NULL, $this->db()->query('select convert(numeric(8), NULL) as value')->next('value'));
    }

    /**
     * Test selecting numeric values
     *
     */
    #[@test]
    public function selectNumeric() {
      $this->assertEquals(1, $this->db()->query('select convert(numeric(8), 1) as value')->next('value'));
    }

    /**
     * Test selecting numeric values
     *
     */
    #[@test]
    public function selectNumericZero() {
      $this->assertEquals(0, $this->db()->query('select convert(numeric(8), 0) as value')->next('value'));
    }

    /**
     * Test selecting numeric values
     *
     */
    #[@test]
    public function selectNegativeNumeric() {
      $this->assertEquals(-6100, $this->db()->query('select convert(numeric(8), -6100) as value')->next('value'));
    }

    /**
     * Test selecting numeric(n, m) values
     *
     */
    #[@test]
    public function selectNumericWithScaleNull() {
      $this->assertEquals(NULL, $this->db()->query('select convert(numeric(8, 2), NULL) as value')->next('value'));
    }

    /**
     * Test selecting numeric(n, m) values
     *
     */
    #[@test]
    public function selectNumericWithScale() {
      $this->assertEquals(1.00, $this->db()->query('select convert(numeric(8, 2), 1) as value')->next('value'));
    }

    /**
     * Test selecting numeric(n, m) values
     *
     */
    #[@test]
    public function selectNumericWithScaleZero() {
      $this->assertEquals(0.00, $this->db()->query('select convert(numeric(8, 2), 0) as value')->next('value'));
    }

    /**
     * Test selecting numeric(n, m) values
     *
     */
    #[@test]
    public function selectNegativeNumericWithScale() {
      $this->assertEquals(-6100.00, $this->db()->query('select convert(numeric(8, 2), -6100) as value')->next('value'));
    }

    /**
     * Test selecting numeric values
     *
     */
    #[@test]
    public function select64BitLongMaxPlus1Numeric() {
      $this->assertEquals('9223372036854775808', $this->db()->query('select convert(numeric(20), 9223372036854775808) as value')->next('value'));
    }

    /**
     * Test selecting numeric values
     *
     */
    #[@test]
    public function select64BitLongMinMinus1Numeric() {
      $this->assertEquals('-9223372036854775809', $this->db()->query('select convert(numeric(20), -9223372036854775809) as value')->next('value'));
    }

    /**
     * Test selecting decimal values
     *
     */
    #[@test]
    public function selectDecimalNull() {
      $this->assertEquals(NULL, $this->db()->query('select convert(decimal(8), NULL) as value')->next('value'));
    }

    /**
     * Test selecting decimal values
     *
     */
    #[@test]
    public function selectDecimal() {
      $this->assertEquals(1, $this->db()->query('select convert(decimal(8), 1) as value')->next('value'));
    }

    /**
     * Test selecting decimal values
     *
     */
    #[@test]
    public function selectDecimalZero() {
      $this->assertEquals(0, $this->db()->query('select convert(decimal(8), 0) as value')->next('value'));
    }

    /**
     * Test selecting decimal values
     *
     */
    #[@test]
    public function selectNegativeDecimal() {
      $this->assertEquals(-6100, $this->db()->query('select convert(decimal(8), -6100) as value')->next('value'));
    }

    /**
     * Test selecting decimal(n, m) values
     *
     */
    #[@test]
    public function selectDecimalWithScaleNull() {
      $this->assertEquals(NULL, $this->db()->query('select convert(decimal(8, 2), NULL) as value')->next('value'));
    }

    /**
     * Test selecting decimal(n, m) values
     *
     */
    #[@test]
    public function selectDecimalWithScale() {
      $this->assertEquals(1.00, $this->db()->query('select convert(decimal(8, 2), 1) as value')->next('value'));
    }

    /**
     * Test selecting decimal(n, m) values
     *
     */
    #[@test]
    public function selectDecimalWithScaleZero() {
      $this->assertEquals(0.00, $this->db()->query('select convert(decimal(8, 2), 0) as value')->next('value'));
    }

    /**
     * Test selecting decimal(n, m) values
     *
     */
    #[@test]
    public function selectNegativeDecimalWithScale() {
      $this->assertEquals(-6100.00, $this->db()->query('select convert(decimal(8, 2), -6100) as value')->next('value'));
    }

    /**
     * Test selecting char values
     *
     */
    #[@test]
    public function selectEmptyChar() {
      $this->assertEquals('    ', $this->db()->query('select cast("" as char(4)) as value')->next('value'));
    }

    /**
     * Test selecting char values
     *
     */
    #[@test]
    public function selectCharWithoutPadding() {
      $this->assertEquals('test', $this->db()->query('select cast("test" as char(4)) as value')->next('value'));
    }

    /**
     * Test selecting char values
     *
     */
    #[@test]
    public function selectCharWithPadding() {
      $this->assertEquals('t   ', $this->db()->query('select cast("t" as char(4)) as value')->next('value'));
    }

    /**
     * Test selecting varchar values
     *
     */
    #[@test]
    public function selectEmptyVarChar() {
      $this->assertEquals('', $this->db()->query('select cast("" as varchar(255)) as value')->next('value'));
    }

    /**
     * Test selecting char values
     *
     */
    #[@test]
    public function selectVarChar() {
      $this->assertEquals('test', $this->db()->query('select cast("test" as varchar(255)) as value')->next('value'));
    }

    /**
     * Test selecting char values
     *
     */
    #[@test]
    public function selectNullVarChar() {
      $this->assertEquals(NULL, $this->db()->query('select cast(NULL as varchar(255)) as value')->next('value'));
    }

    /**
     * Test selecting text values
     *
     */
    #[@test]
    public function selectEmptyText() {
      $this->assertEquals('', $this->db()->query('select cast("" as text) as value')->next('value'));
    }

    /**
     * Test selecting char values
     *
     */
    #[@test]
    public function selectText() {
      $this->assertEquals('test', $this->db()->query('select cast("test" as text) as value')->next('value'));
    }

    /**
     * Test selecting char values
     *
     */
    #[@test]
    public function selectUmlautText() {
      $this->assertEquals('�bercoder', $this->db()->query('select cast("�bercoder" as text) as value')->next('value'));
    }

    /**
     * Test selecting char values
     *
     */
    #[@test]
    public function selectNulltext() {
      $this->assertEquals(NULL, $this->db()->query('select cast(NULL as text) as value')->next('value'));
    }

    /**
     * Test selecting Image values
     *
     */
    #[@test]
    public function selectEmptyImage() {
      $this->assertEquals('', $this->db()->query('select cast("" as image) as value')->next('value'));
    }

    /**
     * Test selecting char values
     *
     */
    #[@test]
    public function selectImage() {
      $this->assertEquals('test', $this->db()->query('select cast("test" as image) as value')->next('value'));
    }

    /**
     * Test selecting char values
     *
     */
    #[@test]
    public function selectUmlautImage() {
      $this->assertEquals('�bercoder', $this->db()->query('select cast("�bercoder" as image) as value')->next('value'));
    }

    /**
     * Test selecting char values
     *
     */
    #[@test]
    public function selectNullImage() {
      $this->assertEquals(NULL, $this->db()->query('select cast(NULL as image) as value')->next('value'));
    }


    /**
     * Test selecting binary values
     *
     */
    #[@test]
    public function selectEmptyBinary() {
      $this->assertEquals('', $this->db()->query('select cast("" as binary) as value')->next('value'));
    }

    /**
     * Test selecting char values
     *
     */
    #[@test]
    public function selectBinary() {
      $this->assertEquals('test', $this->db()->query('select cast("test" as binary) as value')->next('value'));
    }

    /**
     * Test selecting char values
     *
     */
    #[@test]
    public function selectUmlautBinary() {
      $this->assertEquals('�bercoder', $this->db()->query('select cast("�bercoder" as binary) as value')->next('value'));
    }

    /**
     * Test selecting char values
     *
     */
    #[@test]
    public function selectNullBinary() {
      $this->assertEquals(NULL, $this->db()->query('select cast(NULL as binary) as value')->next('value'));
    }

    /**
     * Test selecting varbinary values
     *
     */
    #[@test]
    public function selectEmptyVarBinary() {
      $this->assertEquals('', $this->db()->query('select cast("" as varbinary) as value')->next('value'));
    }

    /**
     * Test selecting char values
     *
     */
    #[@test]
    public function selectVarBinary() {
      $this->assertEquals('test', $this->db()->query('select cast("test" as varbinary) as value')->next('value'));
    }

    /**
     * Test selecting char values
     *
     */
    #[@test]
    public function selectUmlautVarBinary() {
      $this->assertEquals('�bercoder', $this->db()->query('select cast("�bercoder" as varbinary) as value')->next('value'));
    }

    /**
     * Test selecting char values
     *
     */
    #[@test]
    public function selectNullVarBinary() {
      $this->assertEquals(NULL, $this->db()->query('select cast(NULL as varbinary) as value')->next('value'));
    }

    /**
     * Test selecting money values
     *
     */
    #[@test]
    public function selectMoney() {
      $this->assertEquals(0.5, $this->db()->query('select $0.5 as value')->next('value'));
    }

    /**
     * Test selecting money values
     *
     */
    #[@test]
    public function selectHugeMoney() {
      $this->assertEquals(2147483648.0, $this->db()->query('select $2147483648 as value')->next('value'));
    }

    /**
     * Test selecting money values
     *
     */
    #[@test]
    public function selectMoneyOne() {
      $this->assertEquals(1.0, $this->db()->query('select $1.0 as value')->next('value'));
    }

    /**
     * Test selecting money values
     *
     */
    #[@test]
    public function selectMoneyZero() {
      $this->assertEquals(0.0, $this->db()->query('select $0.0 as value')->next('value'));
    }

    /**
     * Test selecting money values
     *
     */
    #[@test]
    public function selectNegativeMoney() {
      $this->assertEquals(-6.1, $this->db()->query('select -$6.1 as value')->next('value'));
    }

    /**
     * Test selecting an unsigned int
     *
     */
    #[@test]
    public function selectUnsignedInt() {
      $this->assertEquals(1, $this->db()->query('select cast(1 as unsigned integer) as value')->next('value'));
    }

    /**
     * Test selecting an unsigned bigint
     *
     */
    #[@test]
    public function selectMaxUnsignedBigInt() {
      $this->assertEquals('18446744073709551615', $this->db()->query('select cast(18446744073709551615 as unsigned bigint) as value')->next('value'));
    }

    /**
     * Test selecting tinyint values
     *
     */
    #[@test]
    public function selectTinyint() {
      $this->assertEquals(5, $this->db()->query('select cast(5 as tinyint) as value')->next('value'));
    }

    /**
     * Test selecting tinyint values
     *
     */
    #[@test]
    public function selectTinyintOne() {
      $this->assertEquals(1, $this->db()->query('select cast(1 as tinyint) as value')->next('value'));
    }

    /**
     * Test selecting tinyint values
     *
     */
    #[@test]
    public function selectTinyintZero() {
      $this->assertEquals(0, $this->db()->query('select cast(0 as tinyint) as value')->next('value'));
    }

    /**
     * Test selecting smallint values
     *
     */
    #[@test]
    public function selectSmallint() {
      $this->assertEquals(5, $this->db()->query('select cast(5 as smallint) as value')->next('value'));
    }

    /**
     * Test selecting smallint values
     *
     */
    #[@test]
    public function selectSmallintOne() {
      $this->assertEquals(1, $this->db()->query('select cast(1 as smallint) as value')->next('value'));
    }

    /**
     * Test selecting smallint values
     *
     */
    #[@test]
    public function selectSmallintZero() {
      $this->assertEquals(0, $this->db()->query('select cast(0 as smallint) as value')->next('value'));
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
      $this->createTransactionsTable($this->tableName());
      $db= $this->db();

      $tran= $db->begin(new Transaction('test'));
      $db->insert('into %c values (1, "should_not_be_here")', $this->tableName());
      $tran->rollback();
      
      $this->assertEquals(
        array(), 
        $db->select('* from %c',$this->tableName())
      );
    }


    /**
     * Test transactions
     *
     */
    #[@test]
    public function committedTransaction() {
      $this->createTransactionsTable($this->tableName());
      $db= $this->db();

      $tran= $db->begin(new Transaction('test'));
      $db->insert('into %c values (1, "should_be_here")', $this->tableName());
      $tran->commit();
      
      $this->assertEquals(
        array(array('pk' => 1, 'username' => 'should_be_here')), 
        $db->select('* from %c', $this->tableName())
      );
    }

    /**
     * Test not reading until the end of a non-buffered result
     *
     */
    #[@test]
    public function unbufferedReadNoResults() {
      $this->createTable();
      $db= $this->db();

      $db->open('select * from %c', $this->tableName());

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

      $q= $db->open('select * from %c', $this->tableName());
      $this->assertEquals(array('pk' => 1, 'username' => 'kiesel'), $q->next());

      $this->assertEquals(1, $db->query('select 1 as num')->next('num'));
    }

    /**
     * Test arithmetic overflow
     *
     */
    #[@test, @expect('rdbms.SQLException')]
    public function arithmeticOverflowWithQuery() {
      $this->db()->query('select cast(10000000000000000 as int)')->next();
    }

    /**
     * Test arithmetic overflow
     *
     */
    #[@test, @expect('rdbms.SQLException')]
    public function arithmeticOverflowWithOpen() {
      $this->db()->open('select cast(10000000000000000 as int)')->next();
    }

    /**
     * Creates fixture for next two tests
     *
     * @return  string SQL
     */
    protected function rowFailureFixture() {
      $this->removeTable($this->tableName());
      $this->db()->query('create table %c (i varchar(20))', $this->tableName());
      $this->db()->insert('into %c values ("1")', $this->tableName());
      $this->db()->insert('into %c values ("not-a-number")', $this->tableName());
      $this->db()->insert('into %c values ("2")', $this->tableName());
      return $this->db()->prepare('select cast(i as int) as i from %c', $this->tableName());
    }

    /**
     * Test 
     *
     */
    #[@test]
    public function readingRowFailsWithQuery() {
      $q= $this->db()->query($this->rowFailureFixture());
      $records= array();
      do {
        try {
          $r= $q->next('i');
          if ($r) $records[]= $r;
        } catch (SQLException $e) {
          $records[]= FALSE;
        }
      } while ($r);
      $this->assertEquals(array(1, FALSE), $records);
    }

    /**
     * Test 
     *
     */
    #[@test]
    public function readingRowFailsWithOpen() {
      $q= $this->db()->open($this->rowFailureFixture());
      $records= array();
      do {
        try {
          $r= $q->next('i');
          if ($r) $records[]= $r;
        } catch (SQLException $e) {
          $records[]= FALSE;
        }
      } while ($r);
      $this->assertEquals(array(1, FALSE), $records);
    }
  }
?>
