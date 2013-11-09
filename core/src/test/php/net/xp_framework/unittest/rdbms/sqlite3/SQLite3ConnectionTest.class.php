<?php namespace net\xp_framework\unittest\rdbms\sqlite3;

use rdbms\sqlite3\SQLite3Connection;

/**
 * Testcase for rdbms.sqlite3.SQLite3Connection
 *
 * @see      xp://rdbms.sqlite3.SQLite3Connection
 */
#[@action(new \unittest\actions\ExtensionAvailable('sqlite3'))]
class SQLite3ConnectionTest extends \unittest\TestCase {
  protected $conn= null;

  /**
   * Set up this test
   */
  public function setUp() {
    $this->conn= new SQLite3Connection(new \rdbms\DSN('sqlite+3:///:memory:?autoconnect=1'));
  }

  #[@test]
  public function close() {
    $this->assertFalse($this->conn->close());
  }

  #[@test]
  public function connect_then_close_both_return_true() {
    $this->assertTrue($this->conn->connect());
    $this->assertTrue($this->conn->close());
  }

  #[@test]
  public function second_close_call_returns_false() {
    $this->assertTrue($this->conn->connect());
    $this->assertTrue($this->conn->close());
    $this->assertFalse($this->conn->close());
  }

  #[@test, @expect('rdbms.SQLStatementFailedException')]
  public function selectdb() {
    $this->conn->selectdb('foo');
  }

  #[@test]
  public function query() {
    $this->conn->connect();
    $result= $this->conn->query('select 1 as one');
    
    $this->assertInstanceOf('rdbms.sqlite3.SQLite3ResultSet', $result);
    $this->assertEquals(array('one' => 1), $result->next());
  }

  #[@test]
  public function query_returns_true_on_empty_resultset() {
    $this->conn->connect();
    $this->assertTrue($this->conn->query('pragma user_version = 1'));
  }

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

  #[@test]
  public function query_returns_result_for_empty_resultset() {
    $this->conn->connect();
    $result= $this->conn->query('select 1 where 1 = 0');

    $this->assertInstanceOf('rdbms.sqlite3.SQLite3ResultSet', $result);
    $this->assertFalse($result->next());
  }

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

  #[@test, @expect('lang.IllegalStateException')]
  public function unbuffered_query_not_supported() {
    $this->conn->setFlag(DB_UNBUFFERED);
    $this->conn->connect();
    $this->conn->query('select 1');
  }

  #[@test, @expect('rdbms.SQLStateException')]
  public function identity_throws_exception_when_not_connected() {
    $this->conn->identity();
  }
 }
