<?php namespace net\xp_framework\unittest\rdbms;

use unittest\TestCase;
use rdbms\DriverManager;
use rdbms\finder\GenericFinder;
use net\xp_framework\unittest\rdbms\dataset\Job;
use net\xp_framework\unittest\rdbms\dataset\JobFinder;
use net\xp_framework\unittest\rdbms\mock\MockResultSet;

/**
 * TestCase
 *
 * @see      xp://rdbms.finder.Finder
 */
#[@action(new \net\xp_framework\unittest\rdbms\mock\RegisterMockConnection())]
class FinderTest extends TestCase {
  protected $fixture = null;

  /**
   * Setup method
   */
  public function setUp() {
    $this->fixture= new JobFinder();
    $this->fixture->getPeer()->setConnection(DriverManager::getConnection('mock://mock/JOBS?autoconnect=1'));
  }

  /**
   * Helper method which invokes the finder's method() method and un-wraps
   * exceptions thrown.
   *
   * @param   string $name
   * @return  rdbms.finder.FinderMethod
   * @throws  lang.Throwable
   */
  protected function method($name) {
    try {
      return $this->fixture->method($name);
    } catch (\rdbms\finder\FinderException $e) {
      throw $e->getCause();
    }
  }

  /**
   * Helper methods
   *
   * @return  net.xp_framework.unittest.rdbms.mock.MockConnection
   */
  protected function getConnection() {
    return $this->fixture->getPeer()->getConnection();
  }

  #[@test]
  public function peerObject() {
    $this->assertClass($this->fixture->getPeer(), 'rdbms.Peer');
  }

  #[@test]
  public function jobPeer() {
    $this->assertEquals($this->fixture->getPeer(), Job::getPeer());
  }

  #[@test]
  public function entityMethods() {
    $methods= $this->fixture->entityMethods();
    $this->assertEquals(1, sizeof($methods));
    $this->assertClass($methods[0], 'rdbms.finder.FinderMethod');
    $this->assertEquals(ENTITY, $methods[0]->getKind());
    $this->assertEquals('byPrimary', $methods[0]->getName());
    $this->assertSubClass($methods[0]->invoke(array($pk= 1)), 'rdbms.SQLExpression');
  }

  #[@test]
  public function collectionMethods() {
    static $invocation= array(
      'all'         => array(),
      'newestJobs'  => array(),
      'expiredJobs' => array(),
      'similarTo'   => array('Test')
    );

    $methods= $this->fixture->collectionMethods();
    $this->assertEquals(4, sizeof($methods)); // three declared plu all()
    foreach ($methods as $method) {
      $this->assertClass($method, 'rdbms.finder.FinderMethod');
      $name= $method->getName();
      $this->assertEquals(COLLECTION, $method->getKind(), $name);
      $this->assertEquals(true, isset($invocation[$name]), $name);
      $this->assertSubClass($method->invoke($invocation[$name]), 'rdbms.SQLExpression', $name);
    }
  }

  #[@test]
  public function allMethods() {
    $methods= $this->fixture->allMethods(); // four declared plu all()
    $this->assertEquals(5, sizeof($methods));
  }

  #[@test]
  public function byPrimaryMethod() {
    $method= $this->fixture->method('byPrimary');
    $this->assertClass($method, 'rdbms.finder.FinderMethod');
    $this->assertEquals('byPrimary', $method->getName());
    $this->assertEquals(ENTITY, $method->getKind());
  }
  
  #[@test, @expect('lang.MethodNotImplementedException')]
  public function nonExistantMethod() {
    $this->method('@@NON-EXISTANT@@');
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function notAFinderMethod() {
    $this->method('getPeer');
  }
  
  #[@test]
  public function findByExistingPrimary() {
    $this->getConnection()->setResultSet(new MockResultSet(array(
      0 => array(   // First row
        'job_id'      => 1,
        'title'       => $this->getName(),
        'valid_from'  => \util\Date::now(),
        'expire_at'   => null
      )
    )));
    $entity= $this->fixture->find($this->fixture->byPrimary(1));
    $this->assertClass($entity, 'net.xp_framework.unittest.rdbms.dataset.Job');
  }

  #[@test]
  public function findByExistingPrimaryFluent() {
    $this->getConnection()->setResultSet(new MockResultSet(array(
      0 => array(   // First row
        'job_id'      => 1,
        'title'       => $this->getName(),
        'valid_from'  => \util\Date::now(),
        'expire_at'   => null
      )
    )));
    $entity= $this->fixture->find()->byPrimary(1);
    $this->assertClass($entity, 'net.xp_framework.unittest.rdbms.dataset.Job');
  }

  #[@test]
  public function findByNonExistantPrimary() {
    $this->assertNull($this->fixture->find($this->fixture->byPrimary(0)));
  }

  #[@test, @expect('rdbms.finder.FinderException')]
  public function findUnexpectedResults() {
    $this->getConnection()->setResultSet(new MockResultSet(array(
      0 => array(   // First row
        'job_id'      => 1,
        'title'       => $this->getName(),
        'valid_from'  => \util\Date::now(),
        'expire_at'   => null
      ),
      1 => array(   // Second row
        'job_id'      => 2,
        'title'       => $this->getName().' #2',
        'valid_from'  => \util\Date::now(),
        'expire_at'   => null
      )
    )));
    $this->fixture->find($this->fixture->byPrimary(1));
  }

  #[@test]
  public function getByExistingPrimary() {
    $this->getConnection()->setResultSet(new MockResultSet(array(
      0 => array(   // First row
        'job_id'      => 1,
        'title'       => $this->getName(),
        'valid_from'  => \util\Date::now(),
        'expire_at'   => null
      )
    )));
    $entity= $this->fixture->get($this->fixture->byPrimary(1));
    $this->assertClass($entity, 'net.xp_framework.unittest.rdbms.dataset.Job');
  }

  #[@test]
  public function getByExistingPrimaryFluent() {
    $this->getConnection()->setResultSet(new MockResultSet(array(
      0 => array(   // First row
        'job_id'      => 1,
        'title'       => $this->getName(),
        'valid_from'  => \util\Date::now(),
        'expire_at'   => null
      )
    )));
    $entity= $this->fixture->get()->byPrimary(1);
    $this->assertClass($entity, 'net.xp_framework.unittest.rdbms.dataset.Job');
  }

  #[@test, @expect('rdbms.finder.NoSuchEntityException')]
  public function getByNonExistantPrimary() {
    $this->fixture->get($this->fixture->byPrimary(0));
  }

  #[@test, @expect('rdbms.finder.FinderException')]
  public function getUnexpectedResults() {
    $this->getConnection()->setResultSet(new MockResultSet(array(
      0 => array(   // First row
        'job_id'      => 1,
        'title'       => $this->getName(),
        'valid_from'  => \util\Date::now(),
        'expire_at'   => null
      ),
      1 => array(   // Second row
        'job_id'      => 2,
        'title'       => $this->getName().' #2',
        'valid_from'  => \util\Date::now(),
        'expire_at'   => null
      )
    )));
    $this->fixture->get($this->fixture->byPrimary(1));
  }

  #[@test]
  public function findNewestJobs() {
    $this->getConnection()->setResultSet(new MockResultSet(array(
      0 => array(   // First row
        'job_id'      => 1,
        'title'       => $this->getName(),
        'valid_from'  => \util\Date::now(),
        'expire_at'   => null
      ),
      1 => array(   // Second row
        'job_id'      => 2,
        'title'       => $this->getName().' #2',
        'valid_from'  => \util\Date::now(),
        'expire_at'   => null
      )
    )));
    $collection= $this->fixture->findAll($this->fixture->newestJobs());
    $this->assertEquals(2, sizeof($collection));
  }

  #[@test]
  public function findNewestJobsFluent() {
    $this->getConnection()->setResultSet(new MockResultSet(array(
      0 => array(   // First row
        'job_id'      => 1,
        'title'       => $this->getName(),
        'valid_from'  => \util\Date::now(),
        'expire_at'   => null
      ),
      1 => array(   // Second row
        'job_id'      => 2,
        'title'       => $this->getName().' #2',
        'valid_from'  => \util\Date::now(),
        'expire_at'   => null
      )
    )));
    $collection= $this->fixture->findAll()->newestJobs();
    $this->assertEquals(2, sizeof($collection));
  }

  #[@test]
  public function getNewestJobs() {
    $this->getConnection()->setResultSet(new MockResultSet(array(
      0 => array(   // First row
        'job_id'      => 1,
        'title'       => $this->getName(),
        'valid_from'  => \util\Date::now(),
        'expire_at'   => null
      ),
      1 => array(   // Second row
        'job_id'      => 2,
        'title'       => $this->getName().' #2',
        'valid_from'  => \util\Date::now(),
        'expire_at'   => null
      )
    )));
    $collection= $this->fixture->getAll($this->fixture->newestJobs());
    $this->assertEquals(2, sizeof($collection));
  }

  #[@test]
  public function getNewestJobsFluent() {
    $this->getConnection()->setResultSet(new MockResultSet(array(
      0 => array(   // First row
        'job_id'      => 1,
        'title'       => $this->getName(),
        'valid_from'  => \util\Date::now(),
        'expire_at'   => null
      ),
      1 => array(   // Second row
        'job_id'      => 2,
        'title'       => $this->getName().' #2',
        'valid_from'  => \util\Date::now(),
        'expire_at'   => null
      )
    )));
    $collection= $this->fixture->getAll()->newestJobs();
    $this->assertEquals(2, sizeof($collection));
  }

  #[@test, @expect('rdbms.finder.NoSuchEntityException')]
  public function getNothingFound() {
    $this->fixture->getAll($this->fixture->newestJobs());
  }

  #[@test, @expect('rdbms.finder.FinderException')]
  public function findWrapsSQLException() {
    $this->getConnection()->makeQueryFail(6010, 'Not enough power');
    $this->fixture->find(new \rdbms\Criteria());
  }

  #[@test, @expect('rdbms.finder.FinderException')]
  public function findAllWrapsSQLException() {
    $this->getConnection()->makeQueryFail(6010, 'Not enough power');
    $this->fixture->findAll(new \rdbms\Criteria());
  }

  #[@test, @expect(class= 'lang.Error', withMessage= '/Call to undefined method .+JobFinder::nonExistantMethod/')]
  public function fluentNonExistantFinder() {
    $this->fixture->findAll()->nonExistantMethod(new \rdbms\Criteria());
  }

  #[@test]
  public function genericFinderGetAll() {
    $this->getConnection()->setResultSet(new MockResultSet(array(
      0 => array(   // First row
        'job_id'      => 1,
        'title'       => $this->getName(),
        'valid_from'  => \util\Date::now(),
        'expire_at'   => null
      ),
      1 => array(   // Second row
        'job_id'      => 2,
        'title'       => $this->getName().' #2',
        'valid_from'  => \util\Date::now(),
        'expire_at'   => null
      )
    )));
    $all= create(new GenericFinder(Job::getPeer()))->getAll(new \rdbms\Criteria());
    $this->assertEquals(2, sizeof($all));
    $this->assertClass($all[0], 'net.xp_framework.unittest.rdbms.dataset.Job');
    $this->assertClass($all[1], 'net.xp_framework.unittest.rdbms.dataset.Job');
  }
}
