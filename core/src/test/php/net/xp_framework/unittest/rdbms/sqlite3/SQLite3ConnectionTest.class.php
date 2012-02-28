<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'rdbms.sqlite3.SQLite3Connection'
  );

  /**
   * Testcase for rdbms.sqlite3.SQLite3Connection
   *
   * @see      xp://rdbms.sqlite3.SQLite3Connection
   */
  class SQLite3ConnectionTest extends TestCase {
    protected $conn= NULL;

    /**
     * Verifies sqlite3 extension is available
     *
     */
    #[@beforeClass]
    public static function verifySqlite3Extension() {
      if (!Runtime::getInstance()->extensionAvailable('sqlite3')) {
        throw new PrerequisitesNotMetError('Extension not available', NULL, array('sqlite3'));
      }
    }

    /**
     * Set up this test
     *
     */
    public function setUp() {
      $this->conn= new Sqlite3Connection(new DSN('sqlite+3:///:memory:?autoconnect=1'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function close() {
      $this->assertFalse($this->conn->close());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function connect_then_close_both_return_true() {
      $this->assertTrue($this->conn->connect());
      $this->assertTrue($this->conn->close());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function second_close_call_returns_false() {
      $this->assertTrue($this->conn->connect());
      $this->assertTrue($this->conn->close());
      $this->assertFalse($this->conn->close());
    }

    /**
     * Test
     *
     */
    #[@test, @expect('rdbms.SQLStatementFailedException')]
    public function selectdb() {
      $this->conn->selectdb('foo');
    }

    /**
     * Test
     *
     */
    #[@test]
    public function query() {
      $this->conn->connect();
      $result= $this->conn->query('select 1 as one');
      
      $this->assertInstanceOf('rdbms.sqlite3.SQLite3ResultSet', $result);
      $this->assertEquals(array('one' => 1), $result->next());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function query_returns_true_on_empty_resultset() {
      $this->conn->connect();
      $this->assertTrue($this->conn->query('pragma user_version = 1'));
    }

    /**
     * Test
     *
     */
    #[@test, @expect('rdbms.SQLStatementFailedException')]
    public function query_throws_exception_for_broken_statement() {
      $this->conn->connect();
      $this->conn->query('select something with wrong syntax');
    }

    /**
     * Unbuffered queries are not supported
     *
     */
    #[@test, @expect('lang.IllegalStateException')]
    public function open_throws_exception() {
      $this->conn->connect();
      $this->conn->open('select 1');
    }

    /**
     * Test
     *
     */
    #[@test]
    public function query_returns_result_for_empty_resultset() {
      $this->conn->connect();
      $result= $this->conn->query('select 1 where 1 = 0');

      $this->assertInstanceOf('rdbms.sqlite3.SQLite3ResultSet', $result);
      $this->assertFalse($result->next());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function create_table_and_fill() {
      $this->conn->query('create temp table testthewest (
        col1 integer primary key asc,
        str2 text,
        col3 real,
        col4 numeric
      )');

      $q= $this->conn->insert('into testthewest (str2, col3, col4) values (%s, %f, %f)',
        "Hello World",
        1.5,
        12345.67
      );
      $this->assertEquals(1, $q); // 1 Row inserted
      $this->assertEquals(1, $this->conn->identity());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function select_from_prefilled_table_yields_correct_column_types() {
      $this->create_table_and_fill();
      $this->assertEquals(array(array(
        'col1' => 1,
        'str2' => 'Hello World',
        'col3' => 1.5,
        'col4' => 12345.67
      )), $this->conn->select('* from testthewest'));
    }

    /**
     * Test
     *
     */
    #[@test, @expect('lang.IllegalStateException')]
    public function unbuffered_query_not_supported() {
      $this->conn->setFlag(DB_UNBUFFERED);
      $this->conn->connect();
      $this->conn->query('select 1');
    }

    /**
     * Test
     *
     */
    #[@test, @expect('rdbms.SQLStateException')]
    public function identity_throws_exception_when_not_connected() {
      $this->conn->identity();
    }
   }
?>
