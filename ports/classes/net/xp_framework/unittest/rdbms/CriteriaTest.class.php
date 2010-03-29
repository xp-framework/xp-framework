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
    'net.xp_framework.unittest.rdbms.dataset.Department',
    'net.xp_framework.unittest.rdbms.dataset.Person',
    'unittest.TestCase'
  );

  /**
   * Test criteria class
   *
   * @see      xp://rdbms.Criteria
   * @purpose  Unit Test
   */
  class CriteriaTest extends TestCase {
    const
      MOCK_CONNECTION_CLASS = 'net.xp_framework.unittest.rdbms.mock.MockConnection';

    public
      $conn = NULL,
      $peer = NULL;

    /**
     * Mock connection registration
     *
     */  
    #[@beforeClass]
    public static function registerMockConnection() {
      DriverManager::register('mock', XPClass::forName(self::MOCK_CONNECTION_CLASS));
    }

    /**
     * Setup method
     *
     */
    public function setUp() {
      $this->conn= DriverManager::getConnection('mock://mock/JOBS?autoconnect=1');
      $this->peer= Job::getPeer();
      $this->peer->setConnection(DriverManager::getConnection('mock://mock/JOBS?autoconnect=1'));
    }
    
    /**
     * Helper method that will call toSQL() on the passed criteria and
     * compare the resulting string to the expected string.
     *
     * @param   string sql
     * @param   rdbms.Criteria criteria
     * @throws  unittest.AssertionFailedError
     */
    protected function assertSql($sql, $criteria) {
      $this->assertEquals($sql, trim($criteria->toSQL($this->conn, $this->peer), ' '));
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
    #[@test, @expect('lang.IllegalArgumentException')]
    public function nonExistantFieldCausesException() {
      $criteria= new Criteria(array('non-existant-field', 1, EQUAL));
      $criteria->toSQL($this->conn, $this->peer);
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
     * Test IN operator
     *
     */
    #[@test]
    public function inCriteria() {
      $c= new Criteria();
      $c->add('job_id', array(1, 2), IN);
      
      $this->assertSql('where job_id in (1, 2)', $c);
    }
    
    /**
     * Test NOT_IN operator
     *
     */
    #[@test]
    public function notInCriteria() {
      $c= new Criteria();
      $c->add('job_id', array(1, 2), NOT_IN);
      
      $this->assertSql('where job_id not in (1, 2)', $c);
    }
    
    /**
     * Test LIKE operator
     *
     */
    #[@test]
    public function likeCriteria() {
      $c= new Criteria();
      $c->add('title', '%keyword%', LIKE);
      
      $this->assertSql('where title like "%keyword%"', $c);
    }
    
    /**
     * Test EQUAL operator
     *
     */
    #[@test]
    public function equalCriteria() {
      $c= new Criteria();
      $c->add('job_id', 1, EQUAL);
      
      $this->assertSql('where job_id = 1', $c);
    }
    
    /**
     * Test NOT_EQUAL operator
     *
     */
    #[@test]
    public function notEqualCriteria() {
      $c= new Criteria();
      $c->add('job_id', 1, NOT_EQUAL);
      
      $this->assertSql('where job_id != 1', $c);
    }
    
    /**
     * Test LESS_THAN operator
     *
     */
    #[@test]
    public function lessThanCriteria() {
      $c= new Criteria();
      $c->add('job_id', 100, LESS_THAN);
      
      $this->assertSql('where job_id < 100', $c);
    }
    
    /**
     * Test GREATER_THAN operator
     *
     */
    #[@test]
    public function greaterThanCriteria() {
      $c= new Criteria();
      $c->add('job_id', 100, GREATER_THAN);
      
      $this->assertSql('where job_id > 100', $c);
    }
    
    /**
     * Test LESS_EQUAL operator
     *
     */
    #[@test]
    public function lessEqualCriteria() {
      $c= new Criteria();
      $c->add('job_id', 100, LESS_EQUAL);
      
      $this->assertSql('where job_id <= 100', $c);
    }
    
    /**
     * Test GREATER_EQUAL operator
     *
     */
    #[@test]
    public function greaterEqualCriteria() {
      $c= new Criteria();
      $c->add('job_id', 100, GREATER_EQUAL);
      
      $this->assertSql('where job_id >= 100', $c);
    }
    
    /**
     * Test BIT_AND operator
     *
     */
    #[@test]
    public function bitAndCriteria() {
      $c= new Criteria();
      $c->add('job_id', 100, BIT_AND);
      
      $this->assertSql('where job_id & 100 != 0', $c);
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
     * @see     xp://rdbms.Criteria#add
     */
    #[@test]
    public function addReturnsThis() {
      $this->assertClass(
        create(new Criteria())->add('job_id', 1, EQUAL), 
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
        create(new Criteria())->add('job_id', 1, EQUAL)->addOrderBy('valid_from', DESCENDING), 
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
        create(new Criteria())->add('job_id', 1, EQUAL)->addGroupBy('valid_from'), 
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
        $c->addOrderBy(Job::column('valid_from'));
        $c->addOrderBy(Job::column('expire_at'));
      }
      $this->assertSql(
        'order by valid_from asc, expire_at asc',
        $c
      );
    }

    /**
     * Tests string as argument for addorderBy
     *
     * @see     xp://rdbms.Criteria#addOrderBy
     */
    #[@test]
    public function addOrderByString() {
      with ($c= new Criteria()); {
        $c->addOrderBy("valid_from");
        $c->addOrderBy("expire_at");
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
        $c->addGroupBy(Job::column('valid_from'));
        $c->addGroupBy(Job::column('expire_at'));
      }
      $this->assertSql(
        'group by valid_from, expire_at',
        $c
      );
    }

    /**
     * Tests string as argument for addGroupBy
     *
     * @see     xp://rdbms.Criteria#addGroupBy
     */
    #[@test]
    public function addGroupByString() {
      with ($c= new Criteria()); {
        $c->addGroupBy("valid_from");
        $c->addGroupBy("expire_at");
      }
      $this->assertSql(
        'group by valid_from, expire_at',
        $c
      );
    }

    /**
     * Tests exception for nonexistant column
     *
     * @see     xp://rdbms.Criteria#addGroupBy
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function createNonExistantColumn() {
      Job::column('not_existant');
    }

    /**
     * Tests exception for nonexistant column
     *
     * @see     xp://rdbms.Criteria#addGroupBy
     */
    #[@test, @expect('rdbms.SQLStateException')]
    public function addGroupByNonExistantColumnString() {
      create(new Criteria())->addGroupBy('not_existant')->toSQL($this->conn, $this->peer);
    }

    /**
     * expect Criteria as result of setFetchmode
     *
     * @see     xp://rdbms.Criteria#addGroupBy
     */
    #[@test]
    public function fetchModeChaining() {
      $this->assertClass(create(new Criteria())->setFetchmode(Fetchmode::join('PersonJob')), 'rdbms.Criteria');
    }

    /**
     * Tests method isJoin
     *
     * @see     xp://rdbms.Criteria#toSQL
     */
    #[@test]
    public function testIsJoin() {
      $crit= new Criteria();
      $this->assertFalse($crit->isJoin());
      $this->assertTrue($crit->setFetchmode(Fetchmode::join('PersonJob'))->isJoin());
      $crit->fetchmode= array();
      $this->assertFalse($crit->isJoin());
      $this->assertFalse($crit->setFetchmode(Fetchmode::select('PersonJob'))->isJoin());
    }

    /**
     * Tests contitions when criteria is a join
     *
     * @see     xp://rdbms.Criteria#toSQL
     */
    #[@test]
    public function testJoinWithoutCondition() {
      $jp= new JoinProcessor(Job::getPeer());
      $jp->setFetchModes(array('PersonJob->Department' => 'join'));
      $jp->enterJoinContext();
      $this->assertEquals(
        '1 = 1',
        create(new Criteria())
        ->setFetchmode(Fetchmode::join('PersonJob'))
        ->toSQL($this->conn, $this->peer)
      );
      $jp->leaveJoinContext();
    }

    /**
     * Tests contitions when criteria is a join
     *
     * @see     xp://rdbms.Criteria#toSQL
     */
    #[@test]
    public function testJoinWithCondition() {
      $jp= new JoinProcessor(Job::getPeer());
      $jp->setFetchModes(array('PersonJob->Department' => 'join'));
      $jp->enterJoinContext();
      $this->assertEquals(
        'PersonJob_Department.department_id = 5 and start.job_id = 2',
        create(new Criteria())
        ->setFetchmode(Fetchmode::join('PersonJob'))
        ->add(Job::column('PersonJob->Department->department_id')->equal(5))
        ->add(Job::column('job_id')->equal(2))
        ->toSQL($this->conn, $this->peer)
      );
      $jp->leaveJoinContext();
    }

    /**
     * test joins and projection
     *
     */
    #[@test]
    public function testJoinWithProjection() {
      $jp= new JoinProcessor(Job::getPeer());
      $jp->setFetchModes(array('PersonJob->Department' => 'join'));
      $jp->enterJoinContext();
      $this->assertEquals(
        'select  PersonJob.job_id, PersonJob_Department.department_id from JOBS.job as start, JOBS.Person as PersonJob, JOBS.Department as PersonJob_Department where start.job_id *= PersonJob.job_id and PersonJob.department_id *= PersonJob_Department.department_id and  1 = 1',
        create(new Criteria())
        ->setFetchmode(Fetchmode::join('PersonJob'))
        ->setProjection(Projections::ProjectionList()
          ->add(Job::column('PersonJob->job_id'))
          ->add(Job::column('PersonJob->Department->department_id'))
        )
        ->getSelectQueryString($this->conn, $this->peer, $jp)
      );
      $jp->leaveJoinContext();
    }

  }
?>
