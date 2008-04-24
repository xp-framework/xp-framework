<?php
/* This class is part of the XP framework
 *
 * $Id: DBXmlGeneratorTest.class.php 9200 2007-01-08 21:55:03Z friebe $ 
 */

  uses(
    'unittest.TestCase',
    'util.Date',
    'rdbms.ConnectionManager',
    'rdbms.SQLFunction',
    'rdbms.criterion.Restrictions',
    'rdbms.SQLDialect',
    'rdbms.join.JoinTable',
    'rdbms.join.JoinRelation',
    'net.xp_framework.unittest.rdbms.dataset.Job'
  );

  /**
   * TestCase
   *
   * @see      rdbms.criterion.Projections
   * @purpose  Unit Tests
   */
  class SQLDialectTest extends TestCase {
    const SYBASE= 'sybase';
    const MYSQL=  'mysql';
  
    public
      $conn=         array(),
      $dialectClass= array(),
      $peer=         NULL;
      
    /**
     * Sets up a Database Object for the test
     *
     */
    public function setUp() {
      $this->conn[self::MYSQL]=  DriverManager::getConnection('mysql://localhost:3306/');
      $this->dialectClass[self::MYSQL]=  'rdbms.mysql.MysqlDialect';

      $this->conn[self::SYBASE]= DriverManager::getConnection('sybase://localhost:1999/');
      $this->dialectClass[self::SYBASE]= 'rdbms.sybase.SybaseDialect';

      $this->peer= Job::getPeer();
    }
    
    /**
     * helper function to test input to makeJoinBy
     *
     * @param util.collections.HashTable[] conditions
     * @param string[] asserts
     */
    private function assertJoin(Array $conditions, Array $asserts) {
      foreach (array_keys($this->conn) as $connName) {
        $dialect= $this->conn[$connName]->getFormatter()->dialect;
        if (!array_key_exists($connName, $asserts)) throw new AssertionFailedError('test for '.$connName.' does not exist in test');
        $this->assertEquals($asserts[$connName], $dialect->makeJoinBy($conditions));
      }
    }
    
    /**
     * test formatter for connection
     *
     */
    #[@test]
    function getFormatterTest() {
      foreach (array_keys($this->conn) as $connName) {
        $this->assertClass(
          $this->conn[$connName]->getFormatter(),
          'rdbms.StatementFormatter'
        );
      }
    }

    /**
     * test dialect interface for formatter
     *
     */
    #[@test]
    function dialectInterfaceTest() {
      foreach (array_keys($this->conn) as $connName) {
        $this->assertTrue($this->conn[$connName]->getFormatter()->dialect instanceof SQLDialect);
      }
    }

    /**
     * test dialect for formatter
     *
     */
    #[@test]
    function dialectTest() {
      foreach (array_keys($this->conn) as $connName) {
        $this->assertClass($this->conn[$connName]->getFormatter()->dialect, $this->dialectClass[$connName]);
      }
    }

    /**
     * test function formatter
     *
     */
    #[@test]
    function functionTest() {
      foreach (array_keys($this->conn) as $connName) {
        $dialect= $this->conn[$connName]->getFormatter()->dialect;
        $this->assertEquals('pi()', $dialect->formatFunction(new SQLFunction('pi', '%s')));
        try {
          $dialect->formatFunction(new SQLFunction('foo', 1,2,3,4,5));
          throw new AssertionFailedError('formatFunction should throw an IllegalArgumentException when calling $dialect->formatFunction(new SQLFunction("foo", 1,2,3,4,5))');
        } catch (IllegalArgumentException $e) {
          $this->assertClass($e, 'lang.IllegalArgumentException');
        }
      }
    }

    /**
     * test function formatter
     *
     */
    #[@test]
    function datepartTest() {
      foreach (array_keys($this->conn) as $connName) {
        $dialect= $this->conn[$connName]->getFormatter()->dialect;
        $this->assertEquals('month', $dialect->datepart('month'));
        try {
          $dialect->datepart('month_foo_bar_buz');
          throw new AssertionFailedError('datepart should throw an IllegalArgumentException when calling $dialect->datepart("month_foo_bar_buz")');
        } catch (IllegalArgumentException $e) {
          $this->assertClass($e, 'lang.IllegalArgumentException');
        }
      }
    }

    /**
     * test datatype formatter
     *
     */
    #[@test]
    function datatypeTest() {
      foreach (array_keys($this->conn) as $connName) {
        $dialect= $this->conn[$connName]->getFormatter()->dialect;
        $this->assertEquals('int', $dialect->datatype('int'));
        try {
          $dialect->datatype('int_foo_bar_buz');
          throw new AssertionFailedError('datatype should throw an IllegalArgumentException when calling $dialect->datatype("int_foo_bar_buz")');
        } catch (IllegalArgumentException $e) {
          $this->assertClass($e, 'lang.IllegalArgumentException');
        }
      }
    }

    /**
     * test join formatter
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    function joinMinimumTest() {
      $asserts= array(
        self::MYSQL  => '',
        self::SYBASE => '',
      );

      $this->assertJoin(
        array(),
        $asserts
      );
    }

    /**
     * test join formatter
     *
     */
    #[@test]
    function joinTwoTablesTest() {
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
     *
     */
    #[@test]
    function joinThreeTablesTest() {
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
