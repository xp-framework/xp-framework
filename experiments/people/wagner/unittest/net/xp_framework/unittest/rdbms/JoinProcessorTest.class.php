<?php
/* This class is part of the XP framework
 *
 * $Id: CriteriaTest.class.php 9319 2007-01-17 15:07:44Z friebe $ 
 */
 
  uses(
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
  class JoinProcessorTest extends TestCase {
  
    /**
     * Constructor
     *
     * @param   string name
     */
    public function __construct($name) {
      parent::__construct($name);
      ConnectionManager::getInstance()->register(DriverManager::getConnection('mysql://localhost:3306/'), 'jobs');
    }
    
    /**
     * Tests extraction from record
     *
     * @see     xp://rdbms.join.JoinProcessor#getAttributeString
     */
    #[@test]
    public function getAttributeStringTest() {
      $jp= new JoinProcessor(Job::getPeer());
      $jp->setFetchModes(array('PersonJob.Department' => 'join'));
      $this->assertEquals(
        $jp->getAttributeString(),
        't0.job_id as t0_job_id, t0.title as t0_title, t0.valid_from as t0_valid_from, t0.expire_at as t0_expire_at, t1.person_id as t1_person_id, t1.name as t1_name, t1.job_id as t1_job_id, t1.department_id as t1_department_id, t2.department_id as t2_department_id, t2.name as t2_name, t2.chief_id as t2_chief_id'
      );
    }

    /**
     * Tests extraction from record
     *
     * @see     xp://rdbms.join.JoinProcessor#getJoinString
     */
    #[@test]
    public function getJoinStringTest() {
      $jp= new JoinProcessor(Job::getPeer());
      $jp->setFetchModes(array('PersonJob.Department' => 'join'));
      $this->assertEquals(
        $jp->getJoinString(),
        'JOBS.job as t0 LEFT OUTER JOIN JOBS.Person as t1 on (t0.job_id = t1.job_id) LEFT JOIN JOBS.Department as t2 on (t1.department_id = t2.department_id) where '
      );
    }

    /**
     * Tests extraction from record
     *
     * @see     xp://rdbms.join.JoinProcessor#setFetchModes
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function emptyModeTest() {
      $jp= new JoinProcessor(Job::getPeer());
      $jp->setFetchModes(array());
    }

    /**
     * Tests extraction from record
     *
     * @see     xp://rdbms.join.JoinProcessor#setFetchModes
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function noJoinModeTest() {
      $jp= new JoinProcessor(Job::getPeer());
      $jp->setFetchModes(array('JobPerson.Department' => 'select'));
    }

    /**
     * Tests extraction from record
     *
     * @see     xp://rdbms.join.JoinProcessor#setFetchModes
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function noSuchRoleTest() {
      $jp= new JoinProcessor(Job::getPeer());
      $jp->setFetchModes(array('UnknowenRole' => 'join'));
    }

  }
?>
