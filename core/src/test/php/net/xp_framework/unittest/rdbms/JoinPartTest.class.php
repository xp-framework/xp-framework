<?php
/* This class is part of the XP framework
 *
 * $Id: CriteriaTest.class.php 9319 2007-01-17 15:07:44Z friebe $ 
 */
 
  uses(
    'rdbms.DSN',
    'rdbms.Criteria',
    'rdbms.mysql.MySQLConnection',
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
  class JoinPartTest extends TestCase {
    public
      $conn = NULL,
      $peer = NULL;
      
    /**
     * Setup test
     *
     */
    public function setUp() {
      $this->conn= new MySQLConnection(new DSN('mysql://localhost:3306/'));
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
      $joinpart= new JoinPart('job', Job::getPeer());
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
      $joinpart= new JoinPart('job', Job::getPeer());
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
      $jobpart=    new JoinPart('j', Job::getPeer());
      $personpart= new JoinPart('p', Person::getPeer());

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
      $toJob=        new JoinPart('j', Job::getPeer());
      $toPerson=     new JoinPart('p', Person::getPeer());
      $toDepartment= new JoinPart('d', Department::getPeer());
      $toChief=      new JoinPart('c', Person::getPeer());

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
      $toJob=        new JoinPart('j', Job::getPeer());
      $toPerson=     new JoinPart('p', Person::getPeer());
      $toDepartment= new JoinPart('d', Department::getPeer());
      $toChief=      new JoinPart('c', Person::getPeer());

      $toJob->addRelative($toPerson, 'JobPerson');
      $toPerson->addRelative($toDepartment, 'Department');
      $toDepartment->addRelative($toChief, 'DepartmentChief');

      $job= Job::getPeer()->objectFor(
        array(
          'job_id'     => '21',
          'title'      => 'clean the toilette',
          'valid_from' => new Date(),
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
