<?php namespace net\xp_framework\unittest\rdbms;
 
use rdbms\Criteria;
use rdbms\DriverManager;
use unittest\TestCase;
use rdbms\join\JoinProcessor;
use rdbms\mysql\MySQLConnection;
use net\xp_framework\unittest\rdbms\dataset\Job;

/**
 * Test JoinProcessor class
 *
 * Note: We're relying on the connection to be a mysql connection -
 * otherwise, quoting and date representation may change and make
 * this testcase fail.
 *
 * @see      xp://rdbms.join.JoinProcessor
 */
class JoinProcessorTest extends TestCase {

  /**
   * Make Job's peer use mysql
   */
  public function setUp() {
    Job::getPeer()->setConnection(new MySQLConnection(new \rdbms\DSN('mysql://localhost:3306/')));
  }
  
  #[@test]
  public function getAttributeStringTest() {
    $jp= new JoinProcessor(Job::getPeer());
    $jp->setFetchModes(array('PersonJob->Department' => 'join'));
    $this->assertEquals(
      $jp->getAttributeString(),
      JoinProcessor::FIRST.'.job_id as '.JoinProcessor::FIRST.'_job_id, '
      .JoinProcessor::FIRST.'.title as '.JoinProcessor::FIRST.'_title, '
      .JoinProcessor::FIRST.'.valid_from as '.JoinProcessor::FIRST.'_valid_from, '
      .JoinProcessor::FIRST.'.expire_at as '.JoinProcessor::FIRST.'_expire_at, '
      .JoinProcessor::pathToKey(array('PersonJob')).'.person_id as '.JoinProcessor::pathToKey(array('PersonJob')).'_person_id, '
      .JoinProcessor::pathToKey(array('PersonJob')).'.name as '.JoinProcessor::pathToKey(array('PersonJob')).'_name, '
      .JoinProcessor::pathToKey(array('PersonJob')).'.job_id as '.JoinProcessor::pathToKey(array('PersonJob')).'_job_id, '
      .JoinProcessor::pathToKey(array('PersonJob')).'.department_id as '.JoinProcessor::pathToKey(array('PersonJob')).'_department_id, '
      .JoinProcessor::pathToKey(array('PersonJob', 'Department')).'.department_id as '.JoinProcessor::pathToKey(array('PersonJob', 'Department')).'_department_id, '
      .JoinProcessor::pathToKey(array('PersonJob', 'Department')).'.name as '.JoinProcessor::pathToKey(array('PersonJob', 'Department')).'_name, '
      .JoinProcessor::pathToKey(array('PersonJob', 'Department')).'.chief_id as '.JoinProcessor::pathToKey(array('PersonJob', 'Department')).'_chief_id'
    );
  }

  #[@test]
  public function getJoinStringTest() {
    $jp= new JoinProcessor(Job::getPeer());
    $jp->setFetchModes(array('PersonJob' => 'join'));
    $jp->setFetchModes(array('PersonJob->Department' => 'join'));
    $this->assertEquals(
      'JOBS.job as '.JoinProcessor::FIRST.' LEFT OUTER JOIN JOBS.Person as '.JoinProcessor::pathToKey(array('PersonJob')).' on ('.JoinProcessor::FIRST.'.job_id = '.JoinProcessor::pathToKey(array('PersonJob')).'.job_id) LEFT JOIN JOBS.Department as '.JoinProcessor::pathToKey(array('PersonJob', 'Department')).' on ('.JoinProcessor::pathToKey(array('PersonJob')).'.department_id = '.JoinProcessor::pathToKey(array('PersonJob', 'Department')).'.department_id) where ',
      $jp->getJoinString()
    );
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function emptyModeTest() {
    $jp= new JoinProcessor(Job::getPeer());
    $jp->setFetchModes(array());
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function noJoinModeTest() {
    $jp= new JoinProcessor(Job::getPeer());
    $jp->setFetchModes(array('JobPerson.Department' => 'select'));
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function noSuchRoleTest() {
    $jp= new JoinProcessor(Job::getPeer());
    $jp->setFetchModes(array('UnknownRole' => 'join'));
  }
}
