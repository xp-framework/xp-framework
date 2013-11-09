<?php namespace net\xp_framework\unittest\rdbms;

use unittest\TestCase;
use util\Date;
use rdbms\SQLFunction;
use rdbms\criterion\Restrictions;
use rdbms\SQLDialect;
use rdbms\mysql\MySQLConnection;
use rdbms\sybase\SybaseConnection;
use rdbms\join\JoinTable;
use rdbms\join\JoinRelation;

/**
 * TestCase
 *
 * @see  xp://rdbms.SQLDialect
 */
class SQLDialectTest extends TestCase {
  const SYBASE= 'sybase';
  const MYSQL= 'mysql';

  protected $conn= array();
    
  /**
   * Fill conn and dialectClass members
   */
  public function __construct($name) {
    parent::__construct($name);
    $this->conn[self::MYSQL]= new MySQLConnection(new \rdbms\DSN('mysql://localhost:3306/'));
    $this->conn[self::SYBASE]= new SybaseConnection(new \rdbms\DSN('sybase://localhost:1999/'));
  }

  /**
   * Provides values for next tests
   *
   * @return  var[]
   */
  public function dialects() {
    $r= array();
    foreach ($this->conn as $name => $conn) {
      $r[]= array($conn->getFormatter()->dialect, $name);
    }
    return $r;
  }

  #[@test, @values('dialects')]
  public function dialect_member($dialect) {
    $this->assertInstanceOf('rdbms.SQLDialect', $dialect);
  }

  #[@test, @values('dialects')]
  public function pi_function($dialect) {
    $this->assertEquals('pi()', $dialect->formatFunction(new SQLFunction('pi', '%s')));
  }

  #[@test, @values('dialects'), @expect('lang.IllegalArgumentException')]
  public function unknown_function($dialect) {
    $dialect->formatFunction(new SQLFunction('foo', 1, 2, 3, 4, 5));
  }

  #[@test, @values('dialects')]
  public function month_datepart($dialect) {
    $this->assertEquals('month', $dialect->datepart('month'));
  }

  #[@test, @values('dialects'), @expect('lang.IllegalArgumentException')]
  public function unknown_datepart($dialect) {
    $dialect->datepart('month_foo_bar_buz');
  }

  #[@test, @values('dialects')]
  public function int_datatype($dialect) {
    $this->assertEquals('int', $dialect->datatype('int'));
  }

  #[@test, @values('dialects'), @expect('lang.IllegalArgumentException')]
  public function unknown_datatype($dialect) {
    $dialect->datatype('int_foo_bar_buz');
  }

  #[@test, @values('dialects'), @expect('lang.IllegalArgumentException')]
  public function join_by_empty($dialect) {
    $dialect->makeJoinBy(array());
  }

  #[@test, @values('dialects')]
  public function join_two_tables($dialect, $name) {
    static $asserts= array(
      self::MYSQL  => 'table0 as t0 LEFT OUTER JOIN table1 as t1 on (t0.id1_1 = t0.id1_1 and t0.id1_2 = t0.id1_2) where ',
      self::SYBASE => 'table0 as t0, table1 as t1 where t0.id1_1 *= t0.id1_1 and t0.id1_2 *= t0.id1_2 and ',
    );

    $t0= new JoinTable('table0', 't0');
    $t1= new JoinTable('table1', 't1');

    $this->assertEquals($asserts[$name], $dialect->makeJoinBy(array(
      new JoinRelation($t0, $t1, array('t0.id1_1 = t0.id1_1', 't0.id1_2 = t0.id1_2'))
    )));
  }

  #[@test, @values('dialects')]
  public function join_three_tables($dialect, $name) {
    static $asserts= array(
      self::MYSQL  => 'table0 as t0 LEFT OUTER JOIN table1 as t1 on (t0.id1_1 = t0.id1_1 and t0.id1_2 = t0.id1_2) LEFT JOIN table2 as t2 on (t1.id2_1 = t2.id2_1) where ',
      self::SYBASE => 'table0 as t0, table1 as t1, table2 as t2 where t0.id1_1 *= t0.id1_1 and t0.id1_2 *= t0.id1_2 and t1.id2_1 *= t2.id2_1 and ',
    );

    $t0= new JoinTable('table0', 't0');
    $t1= new JoinTable('table1', 't1');
    $t2= new JoinTable('table2', 't2');

    $this->assertEquals($asserts[$name], $dialect->makeJoinBy(array(
      new JoinRelation($t0, $t1, array('t0.id1_1 = t0.id1_1', 't0.id1_2 = t0.id1_2')),
      new JoinRelation($t1, $t2, array('t1.id2_1 = t2.id2_1'))
    )));
  }
}
