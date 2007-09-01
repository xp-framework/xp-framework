<?php
/* This class is part of the XP framework
 *
 * $Id: CriteriaTest.class.php 9319 2007-01-17 15:07:44Z friebe $ 
 */

  namespace net::xp_framework::unittest::rdbms;
 
  ::uses(
    'unittest.TestCase',
    'rdbms.query.SelectQuery',
    'rdbms.query.UpdateQuery',
    'rdbms.query.DeleteQuery',
    'rdbms.query.SetOperation',
    'rdbms.Criteria',
    'net.xp_framework.unittest.rdbms.mock.MockConnection',
    'net.xp_framework.unittest.rdbms.dataset.Job',
    'net.xp_framework.unittest.rdbms.dataset.Person'
  );

  define('MOCK_CONNECTION_CLASS', 'net.xp_framework.unittest.rdbms.mock.MockConnection');

  /**
   * Test query class
   *
   * @see      xp://rdbms.Query
   * @purpose  Unit Test
   */
  class QueryTest extends unittest::TestCase {

    private
      $qa= NULL,
      $qb= NULL,
      $qas= 'select  job_id, title from JOBS.job  where job_id = 5',
      $qbs= 'select  job_id, name from JOBS.Person',
      $qu= NULL;
      
    /**
     * Static initializer
     *
     */  
    public static function __static() {
      rdbms::DriverManager::register('mock', lang::XPClass::forName(MOCK_CONNECTION_CLASS));
    }
    
    /**
     * Setup method
     *
     */
    public function setUp() {
      rdbms::ConnectionManager::getInstance()->register(
        rdbms::DriverManager::getConnection('mock://mock/JOBS?autoconnect=1'), 
        'jobs'
      );
      $this->qa= new rdbms::query::SelectQuery();
      $this->qa->setPeer(net::xp_framework::unittest::rdbms::dataset::Job::getPeer());
      $this->qa->setCriteria(
        ::create(new rdbms::Criteria(net::xp_framework::unittest::rdbms::dataset::Job::column('job_id')->equal(5)))->setProjection(
          rdbms::criterion::Projections::ProjectionList()
          ->add(net::xp_framework::unittest::rdbms::dataset::Job::column('job_id'))
          ->add(net::xp_framework::unittest::rdbms::dataset::Job::column('title'))
        )
      );

      $this->qb= new rdbms::query::SelectQuery();
      $this->qb->setPeer(net::xp_framework::unittest::rdbms::dataset::Person::getPeer());
      $this->qb->setCriteria(
        ::create(new rdbms::Criteria())->setProjection(
          rdbms::criterion::Projections::ProjectionList()
          ->add(net::xp_framework::unittest::rdbms::dataset::Person::column('job_id'))
          ->add(net::xp_framework::unittest::rdbms::dataset::Person::column('name'))
        )
      );

    }
    
    /**
     * Test existance
     *
     */
    #[@test]
    public function newQuery() {
      $this->assertTrue(class_exists('SelectQuery'));
    }
    
    /**
     * set and store criteria
     *
     */
    #[@test]
    public function setCriteria() {
      $q= new rdbms::query::SelectQuery();
      $c= new rdbms::Criteria();
      $q->setCriteria($c);
      $this->assertEquals($c, $q->getCriteria());
    }
    
    /**
     * set and store Dataset by name
     *
     */
    #[@test]
    public function setPeer() {
      $q= new rdbms::query::SelectQuery();
      $q->setPeer(net::xp_framework::unittest::rdbms::dataset::Job::getPeer());
      $this->assertEquals(net::xp_framework::unittest::rdbms::dataset::Job::getPeer(), $q->getPeer());
    }
    
    /**
     * get Connection
     *
     */
    #[@test]
    public function getConnection() {
      $q= new rdbms::query::SelectQuery();
      $this->assertNull($q->getConnection());
      $q->setPeer(net::xp_framework::unittest::rdbms::dataset::Job::getPeer());
      $this->assertClass($q->getConnection(), 'net.xp_framework.unittest.rdbms.mock.MockConnection');
    }
    
    /**
     * withRestriction test
     *
     */
    #[@test]
    public function executeWithRestriction() {
      $this->assertClass(::create(new rdbms::query::SelectQuery())->withRestriction(net::xp_framework::unittest::rdbms::dataset::Job::column('job_id')->equal(5)), 'rdbms.query.SelectQuery');
    }
    
    /**
     * test query string without set operation
     *
     */
    #[@test]
    public function getSingleQueryString() {
      $this->assertEquals($this->qas, $this->qa->getQueryString());
      $this->assertEquals($this->qbs, $this->qb->getQueryString());
    }
    
    /**
     * test query string with set operation
     *
     */
    #[@test]
    public function getQueryString() {
      $so= new rdbms::query::SetOperation(rdbms::query::SetOperation::UNION, $this->qa, $this->qb);
      $this->assertEquals(
        $this->qas.' union '.$this->qbs,
        $so->getQueryString()
      );
    }
    
    /**
     * test query string with set operation
     *
     */
    #[@test]
    public function factory() {
      $so= rdbms::query::SetOperation::union($this->qa, $this->qb);
      $this->assertEquals(
        $this->qas.' union '.$this->qbs,
        $so->getQueryString()
      );
      $so= rdbms::query::SetOperation::except($this->qa, $this->qb);
      $this->assertEquals(
        $this->qas.' except '.$this->qbs,
        $so->getQueryString()
      );
      $so= rdbms::query::SetOperation::intercept($this->qa, $this->qb);
      $this->assertEquals(
        $this->qas.' intercept '.$this->qbs,
        $so->getQueryString()
      );
    }
    
    /**
     * test query string with set operation
     *
     */
    #[@test]
    public function all() {
      $so= rdbms::query::SetOperation::union($this->qa, $this->qb, TRUE);
      $this->assertEquals(
        $this->qas.' union all '.$this->qbs,
        $so->getQueryString()
      );
    }
    
    /**
     * test query string with set operation
     *
     */
    #[@test]
    public function nesting() {
      $so= rdbms::query::SetOperation::union(rdbms::query::SetOperation::union($this->qb, $this->qa), rdbms::query::SetOperation::union($this->qb, $this->qa));
      $this->assertEquals(
        $this->qbs.' union '.$this->qas.' union '.$this->qbs.' union '.$this->qas,
        $so->getQueryString()
      );
    }
    
  }
?>
