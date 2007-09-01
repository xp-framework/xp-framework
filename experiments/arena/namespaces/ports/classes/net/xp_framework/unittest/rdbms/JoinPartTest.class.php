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
    'rdbms.join.JoinPart',
    'net.xp_framework.unittest.rdbms.dataset.Job',
    'net.xp_framework.unittest.rdbms.dataset.Person',
    'net.xp_framework.unittest.rdbms.dataset.Department'
  );

  /**
   * Test JoinPart class
   *
   * Note: We're relying on the connection to be a mysql connection -
   * otherwise, quoting and date representation may change and make
   * this testcase fail.
   *
   * @see      xp://rdbms.Criteria
   * @purpose  Unit Test
   */
  class JoinPartTest extends unittest::TestCase {
      
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
      $this->conn= rdbms::DriverManager::getConnection('mysql://localhost:3306/');
    }
    
    /**
     * test an Array
     *
     * @param   mixed[] testArray
     * @param   mixed[] assertArray
     */
    private function assertArrayElements($testArray, $assertArray) {
      $this->assertEquals(sizeof($assertArray), sizeof($testArray));
      foreach ($testArray as $testKey => $testValue) $this->assertEquals($assertArray[$testKey], $testValue);
    }

    
    /**
     * Tests for correct formatted attribute
     *
     * @see     xp://rdbms.join.JoinPart#getAttributes
     */
    #[@test]
    public function getAttributesTest() {
      $joinpart= new rdbms::join::JoinPart('job', net::xp_framework::unittest::rdbms::dataset::Job::getPeer());
      $this->assertArrayElements(
        $joinpart->getAttributes(),
        array(
          'job.job_id as job_job_id',
          'job.title as job_title',
          'job.valid_from as job_valid_from',
          'job.expire_at as job_expire_at' ,
        )
      );
    }

    /**
     * Tests correct formatting for tables
     *
     * @see     xp://rdbms.join.JoinPart#getTable
     */
    #[@test]
    public function getTableTest() {
      $joinpart= new rdbms::join::JoinPart('job', net::xp_framework::unittest::rdbms::dataset::Job::getPeer());
      $this->assertClass(
        $joinpart->getTable(),
        'rdbms.join.JoinTable'
      );
      $this->assertEquals(
        $joinpart->getTable()->toSqlString(),
        'JOBS.job as job'
      );
    }

    /**
     * Tests production of JoinRelations
     *
     * @see     xp://rdbms.join.JoinPart#getJoinRelations
     */
    #[@test]
    public function getJoinRelationsTest() {
      $jobpart=    new rdbms::join::JoinPart('j', net::xp_framework::unittest::rdbms::dataset::Job::getPeer());
      $personpart= new rdbms::join::JoinPart('p', net::xp_framework::unittest::rdbms::dataset::Person::getPeer());

      $jobpart->addRelative($personpart, 'PersonJob');

      $this->assertArray($jobpart->getJoinRelations());
      $j_p= current($jobpart->getJoinRelations());
      $this->assertClass($j_p, 'rdbms.join.JoinRelation');
      $this->assertClass($j_p->getSource(), 'rdbms.join.JoinTable');
      $this->assertClass($j_p->getTarget(), 'rdbms.join.JoinTable');
      $this->assertArrayElements(
        $j_p->getConditions(),
        array('j.job_id = p.job_id')
      );
    }

    /**
     * Tests production of JoinRelations
     *
     * @see     xp://rdbms.join.JoinPart#getJoinRelations
     */
    #[@test]
    public function getComplexJoinRelationsTest() {
      $toJob=        new rdbms::join::JoinPart('j', net::xp_framework::unittest::rdbms::dataset::Job::getPeer());
      $toPerson=     new rdbms::join::JoinPart('p', net::xp_framework::unittest::rdbms::dataset::Person::getPeer());
      $toDepartment= new rdbms::join::JoinPart('d', net::xp_framework::unittest::rdbms::dataset::Department::getPeer());
      $toChief=      new rdbms::join::JoinPart('c', net::xp_framework::unittest::rdbms::dataset::Person::getPeer());

      $toJob->addRelative($toPerson, 'PersonJob');
      $toPerson->addRelative($toDepartment, 'Department');
      $toDepartment->addRelative($toChief, 'Chief');

      $this->assertEquals(
        $this->conn->getFormatter()->dialect->makeJoinBy($toJob->getJoinRelations()),
        'JOBS.job as j LEFT OUTER JOIN JOBS.Person as p on (j.job_id = p.job_id) LEFT JOIN JOBS.Department as d on (p.department_id = d.department_id) LEFT JOIN JOBS.Person as c on (d.chief_id = c.person_id) where '
      );
    }

    /**
     * Tests extraction from record
     *
     * @see     xp://rdbms.join.JoinPart#extract
     */
    #[@test]
    public function extractTest() {
      $toJob=        new rdbms::join::JoinPart('j', net::xp_framework::unittest::rdbms::dataset::Job::getPeer());
      $toPerson=     new rdbms::join::JoinPart('p', net::xp_framework::unittest::rdbms::dataset::Person::getPeer());
      $toDepartment= new rdbms::join::JoinPart('d', net::xp_framework::unittest::rdbms::dataset::Department::getPeer());
      $toChief=      new rdbms::join::JoinPart('c', net::xp_framework::unittest::rdbms::dataset::Person::getPeer());

      $toJob->addRelative($toPerson, 'JobPerson');
      $toPerson->addRelative($toDepartment, 'Department');
      $toDepartment->addRelative($toChief, 'DepartmentChief');

      $job= net::xp_framework::unittest::rdbms::dataset::Job::getPeer()->objectFor(
        array(
          'job_id'     => '21',
          'title'      => 'clean the toilette',
          'valid_from' => new util::Date(),
          'expire_at'  => '',
        )
      );
      $toPerson->extract(
        $job,
        array(
          'p_person_id'     => '11',
          'p_name'          => 'Schultz',
          'p_job_id'        => '21',
          'p_department_id' => '31',
          'd_department_id' => '31',
          'd_name'          => 'iDev',
          'd_chief_id'      => '12',
          'c_person_id'     => '12',
          'c_name'          => 'Friebe',
          'c_job_id'        => '22',
          'c_department_id' => '31',
        ),
        'JobPerson'
      );
      
      $this->assertClass(
        $job->getCachedObj('JobPerson', '#11'),
        'net.xp_framework.unittest.rdbms.dataset.Person'
      );
      $this->assertClass(
        $job->getCachedObj('JobPerson', '#11')->getCachedObj('Department', '#31'),
        'net.xp_framework.unittest.rdbms.dataset.Department'
      );
      $this->assertClass(
        $job->getCachedObj('JobPerson', '#11')->getCachedObj('Department', '#31')->getCachedObj('DepartmentChief', '#12'),
        'net.xp_framework.unittest.rdbms.dataset.Person'
      );

    }

  }
?>
