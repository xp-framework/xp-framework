<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'rdbms.Criteria',
    'rdbms.criterion.Restrictions',
    'rdbms.DriverManager',
    'net.xp_framework.unittest.rdbms.dataset.Job',
    'unittest.TestCase'
  );

  /**
   * Test criteria class
   *
   * Note we're relying on the connection to be a sybase connection -
   * otherwise, quoting and date representation may change and make
   * this testcase fail.
   *
   * @see      xp://rdbms.Criteria
   * @purpose  Unit Test
   */
  class CriteriaTest extends TestCase {
    public
      $conn = NULL,
      $peer = NULL;
      
    /**
     * Constructor
     *
     * @param   string name
     */
    public function __construct($name) {
      parent::__construct($name);
      $this->conn= DriverManager::getConnection('sybase://localhost:1999/');
      $this->peer= Job::getPeer();
    }
    
    /**
     * Helper method that will call toSQL() on the passed criteria and
     * compare the resulting string to the expected string.
     *
     * @param   string sql
     * @param   &rdbms.Criteria criteria
     * @throws  unittest.AssertionFailedError
     */
    protected function assertSql($sql, $criteria) {
      $this->assertEquals($sql, trim($criteria->toSQL($this->conn, $this->peer->types), ' '));
    }
      
    /**
     * Test that an "empty" criteria object will return an empty where 
     * statetement
     *
     */
    #[@test]
    public function emptyCriteria() {
      $this->assertSql('', new Criteria());
    }

    /**
     * Tests a criteria object with one equality comparison
     *
     */
    #[@test]
    public function simpleCriteria() {
      $this->assertSql('where job_id = 1', new Criteria(array('job_id', 1, EQUAL)));
    }

    /**
     * Tests Criteria::toSQL() will throw an exception when using a non-
     * existant field
     *
     */
    #[@test, @expect('rdbms.SQLStateException')]
    public function nonExistantFieldCausesException() {
      $criteria= new Criteria(array('non-existant-field', 1, EQUAL));
      $criteria->toSQL($this->conn, $this->peer->types);
    }

    /**
     * Tests a more complex criteria object
     *
     */
    #[@test]
    public function complexCriteria() {
      with ($c= new Criteria()); {
        $c->add('job_id', 1, EQUAL);
        $c->add('valid_from', new Date('2006-01-01'), GREATER_EQUAL);
        $c->add('title', 'Hello%', LIKE);
        $c->addOrderBy('valid_from');
      }

      $this->assertSql(
        'where job_id = 1 and valid_from >= "2006-01-01 12:00AM" and title like "Hello%" order by valid_from asc', 
        $c
      );
    }
    
    /**
     * Tests the rdbms.criterion API
     *
     * @see     xp://rdbms.Column
     * @see     xp://rdbms.criterion.Restrictions
     */
    #[@test]
    public function restrictionsFactory() {
      $job_id= Job::column('job_id');
      $c= new Criteria(Restrictions::anyOf(
        Restrictions::not($job_id->in(array(1, 2, 3))),
        Restrictions::allOf(
          Job::column('title')->like('Hello%'),
          Job::column('valid_from')->greaterThan(new Date('2006-01-01'))
        ),
        Restrictions::allOf(
          Restrictions::like('title', 'Hello%'),
          Restrictions::greaterThan('valid_from', new Date('2006-01-01'))
        ),
        $job_id->between(1, 5)
      ));

      $this->assertSql(
        'where (not (job_id in (1, 2, 3))'
        .' or (title like "Hello%" and valid_from > "2006-01-01 12:00AM")'
        .' or (title like "Hello%" and valid_from > "2006-01-01 12:00AM")'
        .' or job_id between 1 and 5)',
        $c
      );
    }
    
    /**
     * Tests Criteria constructor for varargs support
     *
     */
    #[@test]
    public function constructorAcceptsVarArgArrays() {
      $this->assertSql(
        'where job_id = 1 and title = "Hello"', 
        new Criteria(array('job_id', 1, EQUAL), array('title', 'Hello', EQUAL))
      );
    }

    /**
     * Tests rdbms.Criteria's fluent interface 
     *
     * @see     xp://rdbms.Criteria#newInstance
     */
    #[@test]
    public function newInstance() {
      $this->assertClass(Criteria::newInstance(), 'rdbms.Criteria');
    }

    /**
     * Tests rdbms.Criteria's fluent interface 
     *
     * @see     xp://rdbms.Criteria#add
     */
    #[@test]
    public function addReturnsThis() {
      $this->assertClass(
        Criteria::newInstance()->add('job_id', 1, EQUAL), 
        'rdbms.Criteria'
      );
    }

    /**
     * Tests rdbms.Criteria's fluent interface 
     *
     * @see     xp://rdbms.Criteria#addOrderBy
     */
    #[@test]
    public function addOrderByReturnsThis() {
      $this->assertClass(
        Criteria::newInstance()->add('job_id', 1, EQUAL)->addOrderBy('valid_from', DESCENDING), 
        'rdbms.Criteria'
      );
    }

    /**
     * Tests rdbms.Criteria's fluent interface 
     *
     * @see     xp://rdbms.Criteria#addGroupBy
     */
    #[@test]
    public function addGroupByReturnsThis() {
      $this->assertClass(
        Criteria::newInstance()->add('job_id', 1, EQUAL)->addGroupBy('valid_from'), 
        'rdbms.Criteria'
      );
    }

    /**
     * Tests rdbms.Column as argument for addorderBy
     *
     * @see     xp://rdbms.Criteria#addOrderBy
     */
    #[@test]
    public function addOrderByColumn() {
      with ($c= new Criteria()); {
        $c->addOrderBy(job::column('valid_from'));
        $c->addOrderBy(job::column('expire_at'));
      }
      $this->assertSql(
        'order by valid_from asc, expire_at asc',
        $c
      );
    }

    /**
     * Tests rdbms.Column as argument for addGroupBy
     *
     * @see     xp://rdbms.Criteria#addGroupBy
     */
    #[@test]
    public function addGroupByColumn() {
      with ($c= new Criteria()); {
        $c->addGroupBy(job::column('valid_from'));
        $c->addGroupBy(job::column('expire_at'));
      }
      $this->assertSql(
        'group by valid_from, expire_at',
        $c
      );
    }

    /**
     * Tests exception for nonexistant column
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function createNonExistantColumn() {
      job::column('not_existant');
    }

    /**
     * Tests exception for nonexistant column
     *
     * @see     xp://rdbms.Criteria#addGroupBy
     */
    #[@test, @expect('rdbms.SQLStateException')]
    public function addGroupByNonExistantColumnString() {
      Criteria::newInstance()->addGroupBy('not_existant')->toSQL($this->conn, $this->peer->types);
    }

    /**
     * Tests exception for nonexistant column
     *
     * @see     xp://rdbms.Criteria#addOrderBy
     */
    #[@test, @expect('rdbms.SQLStateException')]
    public function addOrderByNonExistantColumnString() {
      Criteria::newInstance()->addOrderBy('not_existant')->toSQL($this->conn, $this->peer->types);
    }

  }
?>
