<?php namespace net\xp_framework\unittest\rdbms;
 
use rdbms\DSN;
use rdbms\Criteria;
use rdbms\mysql\MySQLConnection;
use unittest\TestCase;
use rdbms\join\JoinPart;
use net\xp_framework\unittest\rdbms\dataset\Job;
use net\xp_framework\unittest\rdbms\dataset\Person;
use net\xp_framework\unittest\rdbms\dataset\Department;

/**
 * Test JoinPart class
 *
 * Note: We're relying on the connection to be a mysql connection -
 * otherwise, quoting and date representation may change and make
 * this testcase fail.
 *
 * @see     xp://rdbms.Criteria
 */
class JoinPartTest extends TestCase {
  public $conn= null;
  public $peer= null;
    
  /**
   * Setup test
   */
  public function setUp() {
    $this->conn= new MySQLConnection(new DSN('mysql://localhost:3306/'));
  }

  #[@test]
  public function getAttributesTest() {
    $joinpart= new JoinPart('job', Job::getPeer());
    $this->assertEquals(
      $joinpart->getAttributes(),
      array(
        'job.job_id as job_job_id',
        'job.title as job_title',
        'job.valid_from as job_valid_from',
        'job.expire_at as job_expire_at' ,
      )
    );
  }

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
    $this->assertEquals(
      $j_p->getConditions(),
      array('j.job_id = p.job_id')
    );
  }

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
        'valid_from' => new \util\Date(),
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
