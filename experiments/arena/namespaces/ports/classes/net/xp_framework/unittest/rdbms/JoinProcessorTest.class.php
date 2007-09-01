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
    'net.xp_framework.unittest.rdbms.dataset.Job'
  );

  /**
   * Test JoinProcessor class
   *
   * Note: We're relying on the connection to be a mysql connection -
   * otherwise, quoting and date representation may change and make
   * this testcase fail.
   *
   * @see      xp://rdbms.join.JoinProcessor
   * @purpose  Unit Test
   */
  class JoinProcessorTest extends unittest::TestCase {
  
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
     * @see     xp://rdbms.join.JoinProcessor#getAttributeString
     */
    #[@test]
    public function getAttributeStringTest() {
      $jp= new rdbms::join::JoinProcessor(net::xp_framework::unittest::rdbms::dataset::Job::getPeer());
      $jp->setFetchModes(array('PersonJob->Department' => 'join'));
      $this->assertEquals(
        $jp->getAttributeString(),
        rdbms::join::JoinProcessor::FIRST.'.job_id as '.rdbms::join::JoinProcessor::FIRST.'_job_id, '
        .rdbms::join::JoinProcessor::FIRST.'.title as '.rdbms::join::JoinProcessor::FIRST.'_title, '
        .rdbms::join::JoinProcessor::FIRST.'.valid_from as '.rdbms::join::JoinProcessor::FIRST.'_valid_from, '
        .rdbms::join::JoinProcessor::FIRST.'.expire_at as '.rdbms::join::JoinProcessor::FIRST.'_expire_at, '
        .rdbms::join::JoinProcessor::pathToKey(array('PersonJob')).'.person_id as '.rdbms::join::JoinProcessor::pathToKey(array('PersonJob')).'_person_id, '
        .rdbms::join::JoinProcessor::pathToKey(array('PersonJob')).'.name as '.rdbms::join::JoinProcessor::pathToKey(array('PersonJob')).'_name, '
        .rdbms::join::JoinProcessor::pathToKey(array('PersonJob')).'.job_id as '.rdbms::join::JoinProcessor::pathToKey(array('PersonJob')).'_job_id, '
        .rdbms::join::JoinProcessor::pathToKey(array('PersonJob')).'.department_id as '.rdbms::join::JoinProcessor::pathToKey(array('PersonJob')).'_department_id, '
        .rdbms::join::JoinProcessor::pathToKey(array('PersonJob', 'Department')).'.department_id as '.rdbms::join::JoinProcessor::pathToKey(array('PersonJob', 'Department')).'_department_id, '
        .rdbms::join::JoinProcessor::pathToKey(array('PersonJob', 'Department')).'.name as '.rdbms::join::JoinProcessor::pathToKey(array('PersonJob', 'Department')).'_name, '
        .rdbms::join::JoinProcessor::pathToKey(array('PersonJob', 'Department')).'.chief_id as '.rdbms::join::JoinProcessor::pathToKey(array('PersonJob', 'Department')).'_chief_id'
      );
    }

    /**
     * Tests extraction from record
     *
     * @see     xp://rdbms.join.JoinProcessor#getJoinString
     */
    #[@test]
    public function getJoinStringTest() {
      $jp= new rdbms::join::JoinProcessor(net::xp_framework::unittest::rdbms::dataset::Job::getPeer());
      $jp->setFetchModes(array('PersonJob' => 'join'));
      $jp->setFetchModes(array('PersonJob->Department' => 'join'));
      $this->assertEquals(
        $jp->getJoinString(),
        'JOBS.job as '.rdbms::join::JoinProcessor::FIRST.' LEFT OUTER JOIN JOBS.Person as '.rdbms::join::JoinProcessor::pathToKey(array('PersonJob')).' on ('.rdbms::join::JoinProcessor::FIRST.'.job_id = '.rdbms::join::JoinProcessor::pathToKey(array('PersonJob')).'.job_id) LEFT JOIN JOBS.Department as '.rdbms::join::JoinProcessor::pathToKey(array('PersonJob', 'Department')).' on ('.rdbms::join::JoinProcessor::pathToKey(array('PersonJob')).'.department_id = '.rdbms::join::JoinProcessor::pathToKey(array('PersonJob', 'Department')).'.department_id) where '
      );
    }

    /**
     * Tests extraction from record
     *
     * @see     xp://rdbms.join.JoinProcessor#setFetchModes
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function emptyModeTest() {
      $jp= new rdbms::join::JoinProcessor(net::xp_framework::unittest::rdbms::dataset::Job::getPeer());
      $jp->setFetchModes(array());
    }

    /**
     * Tests extraction from record
     *
     * @see     xp://rdbms.join.JoinProcessor#setFetchModes
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function noJoinModeTest() {
      $jp= new rdbms::join::JoinProcessor(net::xp_framework::unittest::rdbms::dataset::Job::getPeer());
      $jp->setFetchModes(array('JobPerson.Department' => 'select'));
    }

    /**
     * Tests extraction from record
     *
     * @see     xp://rdbms.join.JoinProcessor#setFetchModes
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function noSuchRoleTest() {
      $jp= new rdbms::join::JoinProcessor(net::xp_framework::unittest::rdbms::dataset::Job::getPeer());
      $jp->setFetchModes(array('UnknowenRole' => 'join'));
    }

  }
?>
