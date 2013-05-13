<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'util.Date',
    'rdbms.SQLFunction',
    'rdbms.criterion.Restrictions',
    'rdbms.SQLDialect',
    'rdbms.mysql.MySQLConnection',
    'rdbms.sybase.SybaseConnection',
    'rdbms.join.JoinTable',
    'rdbms.join.JoinRelation',
    'net.xp_framework.unittest.rdbms.dataset.Job'
  );

  /**
   * TestCase
   *
   * @see  xp://rdbms.SQLDialect
   */
  class SQLDialectTest extends TestCase {
    const SYBASE= 'sybase';
    const MYSQL= 'mysql';
  
    protected $conn= array();
    protected $dialectClass= array();
      
    /**
     * Fill conn and dialectClass members
     */
    public function __construct($name) {
      parent::__construct($name);
      $this->conn[self::MYSQL]= new MySQLConnection(new DSN('mysql://localhost:3306/'));
      $this->dialectClass[self::MYSQL]= 'rdbms.mysql.MysqlDialect';
      $this->conn[self::SYBASE]= new SybaseConnection(new DSN('sybase://localhost:1999/'));
      $this->dialectClass[self::SYBASE]= 'rdbms.sybase.SybaseDialect';
    }

    /**
     * Provides values for next tests
     *
     * @return  var[]
     */
    public function connections() {
      $r= array();
      foreach ($this->conn as $name => $conn) {
        $r[]= array($conn->getFormatter()->dialect);
      }
      return $r;
    }

    #[@test, @values('connections')]
    public function dialect_member($dialect) {
      $this->assertInstanceOf('rdbms.SQLDialect', $dialect);
    }

    #[@test, @values('connections')]
    public function pi_function($dialect) {
      $this->assertEquals('pi()', $dialect->formatFunction(new SQLFunction('pi', '%s')));
    }

    #[@test, @values('connections'), @expect('lang.IllegalArgumentException')]
    public function unknown_function($dialect) {
      $dialect->formatFunction(new SQLFunction('foo', 1, 2, 3, 4, 5));
    }

    #[@test, @values('connections')]
    public function month_datepart($dialect) {
      $this->assertEquals('month', $dialect->datepart('month'));
    }

    #[@test, @values('connections'), @expect('lang.IllegalArgumentException')]
    public function unknown_datepart($dialect) {
      $dialect->datepart('month_foo_bar_buz');
    }

    #[@test, @values('connections')]
    public function int_datatype($dialect) {
      $this->assertEquals('int', $dialect->datatype('int'));
    }

    #[@test, @values('connections'), @expect('lang.IllegalArgumentException')]
    public function unknown_datatype($dialect) {
      $dialect->datatype('int_foo_bar_buz');
    }

    #[@test, @values('connections'), @expect('lang.IllegalArgumentException')]
    public function join_by_empty($dialect) {
      $dialect->makeJoinBy(array());
    }

    /**
     * helper function to test input to makeJoinBy
     *
     * @param  var[] conditions
     * @param  var[] asserts
     * @throws unittest.AssertionFailedError
     */
    private function assertJoin(array $conditions, array $asserts) {
      foreach (array_keys($this->conn) as $connName) {
        $dialect= $this->conn[$connName]->getFormatter()->dialect;
        if (!array_key_exists($connName, $asserts)) throw new AssertionFailedError('test for '.$connName.' does not exist in test');
        $this->assertEquals($asserts[$connName], $dialect->makeJoinBy($conditions));
      }
    }

    /**
     * test join formatter
     *
     */
    #[@test]
    public function joinTwoTablesTest() {
      $asserts= array(
        self::MYSQL  => 'table0 as t0 LEFT OUTER JOIN table1 as t1 on (t0.id1_1 = t0.id1_1 and t0.id1_2 = t0.id1_2) where ',
        self::SYBASE => 'table0 as t0, table1 as t1 where t0.id1_1 *= t0.id1_1 and t0.id1_2 *= t0.id1_2 and ',
      );

      $t0= new JoinTable('table0', 't0');
      $t1= new JoinTable('table1', 't1');

      $conditions= array(
        create(new JoinRelation($t0, $t1, array('t0.id1_1 = t0.id1_1', 't0.id1_2 = t0.id1_2')))
      );
      
      $this->assertJoin(
        $conditions,
        $asserts
      );
    }

    /**
     * test join formatter
     */
    #[@test]
    public function joinThreeTablesTest() {
      $asserts= array(
        self::MYSQL  => 'table0 as t0 LEFT OUTER JOIN table1 as t1 on (t0.id1_1 = t0.id1_1 and t0.id1_2 = t0.id1_2) LEFT JOIN table2 as t2 on (t1.id2_1 = t2.id2_1) where ',
        self::SYBASE => 'table0 as t0, table1 as t1, table2 as t2 where t0.id1_1 *= t0.id1_1 and t0.id1_2 *= t0.id1_2 and t1.id2_1 *= t2.id2_1 and ',
      );

      $t0= new JoinTable('table0', 't0');
      $t1= new JoinTable('table1', 't1');
      $t2= new JoinTable('table2', 't2');

      $conditions= array(
        create(new JoinRelation($t0, $t1, array('t0.id1_1 = t0.id1_1', 't0.id1_2 = t0.id1_2'))),
        create(new JoinRelation($t1, $t2, array('t1.id2_1 = t2.id2_1'))),
      );
      
      $this->assertJoin(
        $conditions,
        $asserts
      );
    }
  }
?>
