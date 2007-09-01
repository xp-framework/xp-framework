<?php
/* This class is part of the XP framework
 *
 * $Id: CriteriaTest.class.php 9319 2007-01-17 15:07:44Z friebe $ 
 */

  namespace net::xp_framework::unittest::rdbms;
 
  ::uses(
    'rdbms.Criteria',
    'rdbms.DriverManager',
    'unittest.TestCase',
    'rdbms.join.JoinProcessor',
    'rdbms.join.JoinIterator',
    'net.xp_framework.unittest.rdbms.dataset.Job',
    'net.xp_framework.unittest.rdbms.mock.MockResultSet'
  );

  /**
   * Test JoinProcessor class
   *
   * Note: We're relying on the connection to be a mysql connection -
   * otherwise, quoting and date representation may change and make
   * this testcase fail.
   *
   * @see      xp://rdbms.join.JoinIterator
   * @purpose  Unit Test
   */
  class JoinIteratorTest extends unittest::TestCase {
    
    /**
     * Constructor
     *
     * @param   string name
     */
    public function __construct($name) {
      parent::__construct($name);
      rdbms::ConnectionManager::getInstance()->register(rdbms::DriverManager::getConnection('mysql://localhost:3306/'), 'jobs');
    }
    
    /**
     * Tests extraction from record
     *
     * @see     xp://rdbms.join.JoinProcessor#setFetchModes
     */
    #[@test, @expect('util.NoSuchElementException')]
    public function emptyResultNextTest() {
      ::create(new rdbms::join::JoinIterator(new rdbms::join::JoinProcessor(net::xp_framework::unittest::rdbms::dataset::Job::getPeer()), new net::xp_framework::unittest::rdbms::mock::MockResultSet()))->next();
    }
    
    /**
     * Tests extraction from record
     *
     * @see     xp://rdbms.join.JoinProcessor#setFetchModes
     */
    #[@test]
    public function emptyResultHasNextTest() {
      $this->assertFalse(::create(new rdbms::join::JoinIterator(new rdbms::join::JoinProcessor(net::xp_framework::unittest::rdbms::dataset::Job::getPeer()), new net::xp_framework::unittest::rdbms::mock::MockResultSet()))->hasNext());
    }
    
    /**
     * Tests extraction from record
     *
     * @see     xp://rdbms.join.JoinProcessor#setFetchModes
     */
    #[@test]
    public function resultHasNextTest() {
      $rs= new net::xp_framework::unittest::rdbms::mock::MockResultSet(
        array(
          array(
            rdbms::join::JoinProcessor::FIRST.'_job_id'        => '11',
            rdbms::join::JoinProcessor::FIRST.'_title'         => 'clean toilette',
            rdbms::join::JoinProcessor::FIRST.'_valid_from'    => new util::Date(),
            rdbms::join::JoinProcessor::FIRST.'_expire_at'     => '',
            't1_person_id'     => '11',
            't1_name'          => 'Schultz',
            't1_job_id'        => '21',
            't1_department_id' => '31',
          ),
          array(
            rdbms::join::JoinProcessor::FIRST.'_job_id'        => '11',
            rdbms::join::JoinProcessor::FIRST.'_title'         => 'clean toilette',
            rdbms::join::JoinProcessor::FIRST.'_valid_from'    => new util::Date(),
            rdbms::join::JoinProcessor::FIRST.'_expire_at'     => '',
            't1_person_id'     => '12',
            't1_name'          => 'Friebe',
            't1_job_id'        => '11',
            't1_department_id' => '31',
          ),
        )
      );
      $ji= new rdbms::join::JoinIterator(new rdbms::join::JoinProcessor(net::xp_framework::unittest::rdbms::dataset::Job::getPeer()), $rs);
      $this->assertTrue($ji->hasNext());
      $this->assertClass($ji->next(), 'net.xp_framework.unittest.rdbms.dataset.Job');
      $this->assertFalse($ji->hasNext());
    }

    /**
     * Tests extraction from record
     *
     * @see     xp://rdbms.join.JoinProcessor#setFetchModes
     */
    #[@test]
    public function multipleResultTest() {
      $rs= new net::xp_framework::unittest::rdbms::mock::MockResultSet(
        array(
          array(
            rdbms::join::JoinProcessor::FIRST.'_job_id'        => '11',
            rdbms::join::JoinProcessor::FIRST.'_title'         => 'clean toilette',
            rdbms::join::JoinProcessor::FIRST.'_valid_from'    => new util::Date(),
            rdbms::join::JoinProcessor::FIRST.'_expire_at'     => '',
          ),
          array(
            rdbms::join::JoinProcessor::FIRST.'_job_id'        => '11',
            rdbms::join::JoinProcessor::FIRST.'_title'         => 'clean toilette',
            rdbms::join::JoinProcessor::FIRST.'_valid_from'    => new util::Date(),
            rdbms::join::JoinProcessor::FIRST.'_expire_at'     => '',
          ),
          array(
            rdbms::join::JoinProcessor::FIRST.'_job_id'        => '12',
            rdbms::join::JoinProcessor::FIRST.'_title'         => 'second job',
            rdbms::join::JoinProcessor::FIRST.'_valid_from'    => new util::Date(),
            rdbms::join::JoinProcessor::FIRST.'_expire_at'     => '',
          ),
          array(
            rdbms::join::JoinProcessor::FIRST.'_job_id'        => '13',
            rdbms::join::JoinProcessor::FIRST.'_title'         => 'third job',
            rdbms::join::JoinProcessor::FIRST.'_valid_from'    => new util::Date(),
            rdbms::join::JoinProcessor::FIRST.'_expire_at'     => '',
          ),
        )
      );
      $jp= new rdbms::join::JoinProcessor(net::xp_framework::unittest::rdbms::dataset::Job::getPeer());
      $ji= new rdbms::join::JoinIterator($jp, $rs);
      $this->assertTrue($ji->hasNext());
      $this->assertClass($job= $ji->next(), 'net.xp_framework.unittest.rdbms.dataset.Job');
      $this->assertTrue($ji->hasNext());
      $this->assertClass($job= $ji->next(), 'net.xp_framework.unittest.rdbms.dataset.Job');
      $this->assertTrue($ji->hasNext());
      $this->assertClass($job= $ji->next(), 'net.xp_framework.unittest.rdbms.dataset.Job');
      $this->assertFalse($ji->hasNext());
    }

    /**
     * Tests extraction from record
     *
     * @see     xp://rdbms.join.JoinProcessor#setFetchModes
     */
    #[@test]
    public function multipleJoinResultTest() {
      $rs= new net::xp_framework::unittest::rdbms::mock::MockResultSet(
        array(
          array(
            rdbms::join::JoinProcessor::FIRST.'_job_id'        => '11',
            rdbms::join::JoinProcessor::FIRST.'_title'         => 'clean toilette',
            rdbms::join::JoinProcessor::FIRST.'_valid_from'    => new util::Date(),
            rdbms::join::JoinProcessor::FIRST.'_expire_at'     => '',
            rdbms::join::JoinProcessor::pathToKey(array('PersonJob')).'_person_id'     => '11',
            rdbms::join::JoinProcessor::pathToKey(array('PersonJob')).'_name'          => 'Schultz',
            rdbms::join::JoinProcessor::pathToKey(array('PersonJob')).'_job_id'        => '21',
            rdbms::join::JoinProcessor::pathToKey(array('PersonJob')).'_department_id' => '31',
          ),
          array(
            rdbms::join::JoinProcessor::FIRST.'_job_id'        => '11',
            rdbms::join::JoinProcessor::FIRST.'_title'         => 'clean toilette',
            rdbms::join::JoinProcessor::FIRST.'_valid_from'    => new util::Date(),
            rdbms::join::JoinProcessor::FIRST.'_expire_at'     => '',
            rdbms::join::JoinProcessor::pathToKey(array('PersonJob')).'_person_id'     => '12',
            rdbms::join::JoinProcessor::pathToKey(array('PersonJob')).'_name'          => 'Müller',
            rdbms::join::JoinProcessor::pathToKey(array('PersonJob')).'_job_id'        => '11',
            rdbms::join::JoinProcessor::pathToKey(array('PersonJob')).'_department_id' => '31',
          ),
          array(
            rdbms::join::JoinProcessor::FIRST.'_job_id'        => '12',
            rdbms::join::JoinProcessor::FIRST.'_title'         => 'second job',
            rdbms::join::JoinProcessor::FIRST.'_valid_from'    => new util::Date(),
            rdbms::join::JoinProcessor::FIRST.'_expire_at'     => '',
            rdbms::join::JoinProcessor::pathToKey(array('PersonJob')).'_person_id'     => '11',
            rdbms::join::JoinProcessor::pathToKey(array('PersonJob')).'_name'          => 'Schultz',
            rdbms::join::JoinProcessor::pathToKey(array('PersonJob')).'_job_id'        => '21',
            rdbms::join::JoinProcessor::pathToKey(array('PersonJob')).'_department_id' => '31',
          ),
          array(
            rdbms::join::JoinProcessor::FIRST.'_job_id'        => '13',
            rdbms::join::JoinProcessor::FIRST.'_title'         => 'third job',
            rdbms::join::JoinProcessor::FIRST.'_valid_from'    => new util::Date(),
            rdbms::join::JoinProcessor::FIRST.'_expire_at'     => '',
            rdbms::join::JoinProcessor::pathToKey(array('PersonJob')).'_person_id'     => NULL,
            rdbms::join::JoinProcessor::pathToKey(array('PersonJob')).'_name'          => NULL,
            rdbms::join::JoinProcessor::pathToKey(array('PersonJob')).'_job_id'        => NULL,
            rdbms::join::JoinProcessor::pathToKey(array('PersonJob')).'_department_id' => NULL,
          ),
        )
      );
      $jp= new rdbms::join::JoinProcessor(net::xp_framework::unittest::rdbms::dataset::Job::getPeer());
      $jp->setFetchModes(array('PersonJob' => 'join'));
      $ji= new rdbms::join::JoinIterator($jp, $rs);

      $this->assertTrue($ji->hasNext());
      $this->assertClass($job= $ji->next(), 'net.xp_framework.unittest.rdbms.dataset.Job');
      $this->assertArray($job->getPersonJobList());
      $this->assertClass($pji= $job->getPersonJobIterator(), 'util.HashmapIterator');

      $this->assertTrue($pji->hasNext());
      $this->assertClass($pji->next(), 'net.xp_framework.unittest.rdbms.dataset.Person');
      $this->assertTrue($pji->hasNext());
      $this->assertClass($pji->next(), 'net.xp_framework.unittest.rdbms.dataset.Person');
      $this->assertFalse($pji->hasNext());

      $this->assertTrue($ji->hasNext());
      $this->assertClass($job= $ji->next(), 'net.xp_framework.unittest.rdbms.dataset.Job');
      $this->assertArray($job->getPersonJobList());
      $this->assertClass($pji= $job->getPersonJobIterator(), 'util.HashmapIterator');
      $this->assertTrue($pji->hasNext());
      $this->assertClass($pji->next(), 'net.xp_framework.unittest.rdbms.dataset.Person');
      $this->assertFalse($pji->hasNext());

      $this->assertTrue($ji->hasNext());
      $this->assertClass($job= $ji->next(), 'net.xp_framework.unittest.rdbms.dataset.Job');
      $this->assertArray($job->getPersonJobList());
      $this->assertClass($pji= $job->getPersonJobIterator(), 'util.HashmapIterator');
      $this->assertFalse($pji->hasNext());

      $this->assertFalse($ji->hasNext());
    }
  }
?>
