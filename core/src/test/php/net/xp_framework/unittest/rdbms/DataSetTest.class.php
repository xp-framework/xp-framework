<?php namespace net\xp_framework\unittest\rdbms;

use unittest\TestCase;
use rdbms\DriverManager;
use rdbms\DBObserver;
use util\Date;
use util\collections\Vector;
use lang\types\String;
use util\DateUtil;
use rdbms\Statement;
use net\xp_framework\unittest\rdbms\dataset\Job;
use net\xp_framework\unittest\rdbms\mock\MockResultSet;

/**
 * O/R-mapping API unit test
 *
 * @see      xp://rdbms.DataSet
 */
#[@action(new \net\xp_framework\unittest\rdbms\mock\RegisterMockConnection())]
class DataSetTest extends TestCase {
  const IRRELEVANT_NUMBER= -1;

  /**
   * Setup method
   */
  public function setUp() {
    Job::getPeer()->setConnection(DriverManager::getConnection('mock://mock/JOBS?autoconnect=1'));
  }
  
  /**
   * Helper methods
   *
   * @return  net.xp_framework.unittest.rdbms.mock.MockConnection
   */
  protected function getConnection() {
    return Job::getPeer()->getConnection();
  }
  
  /**
   * Helper method
   *
   * @param   net.xp_framework.unittest.rdbms.mock.MockResultSet r
   */
  protected function setResults($r) {
    $this->getConnection()->setResultSet($r);
  }
  
  #[@test]
  public function peerObject() {
    $peer= Job::getPeer();
    $this->assertClass($peer, 'rdbms.Peer');
    $this->assertEquals('net\xp_framework\unittest\rdbms\dataset\job', strtolower($peer->identifier));
    $this->assertEquals('jobs', $peer->connection);
    $this->assertEquals('JOBS.job', $peer->table);
    $this->assertEquals('job_id', $peer->identity);
    $this->assertEquals(
      array('job_id'), 
      $peer->primary
    );
    $this->assertEquals(
      array('job_id', 'title', 'valid_from', 'expire_at'),
      array_keys($peer->types)
    );
  }
  
  #[@test]
  public function getByJob_id() {
    $now= Date::now();
    $this->setResults(new MockResultSet(array(
      0 => array(   // First row
        'job_id'      => 1,
        'title'       => 'Unit tester',
        'valid_from'  => $now,
        'expire_at'   => null
      )
    )));
    $job= Job::getByJob_id(1);
    $this->assertClass($job, 'net.xp_framework.unittest.rdbms.dataset.Job');
    $this->assertEquals(1, $job->getJob_id());
    $this->assertEquals('Unit tester', $job->getTitle());
    $this->assertEquals($now, $job->getValid_from());
    $this->assertNull($job->getExpire_at());
  }
  
  #[@test]
  public function newObject() {
    $j= new Job();
    $this->assertTrue($j->isNew());
  }

  #[@test]
  public function existingObject() {
    $this->setResults(new MockResultSet(array(
      0 => array(   // First row
        'job_id'      => 1,
        'title'       => 'Unit tester',
        'valid_from'  => Date::now(),
        'expire_at'   => null
      )
    )));
    
    $job= Job::getByJob_id(1);
    $this->assertNotEquals(null, $job);
    $this->assertFalse($job->isNew());
  }

  #[@test]
  public function noLongerNewAfterSave() {
    $j= new Job();
    $j->setTitle('New job');
    $j->setValid_from(Date::now());
    $j->setExpire_at(null);
    
    $this->assertTrue($j->isNew());
    $j->save();
    $this->assertFalse($j->isNew());
  }

  #[@test]
  public function noResultsDuringGetByJob_id() {
    $this->setResults(new MockResultSet());
    $this->assertNull(Job::getByJob_id(self::IRRELEVANT_NUMBER));
  }

  #[@test, @expect('rdbms.SQLException')]
  public function failedQueryInGetByJob_id() {
    $mock= $this->getConnection();
    $mock->makeQueryFail(1, 'Select failed');

    Job::getByJob_id(self::IRRELEVANT_NUMBER);
  }

  #[@test]
  public function insertReturnsIdentity() {
    $mock= $this->getConnection();
    $mock->setIdentityValue(14121977);

    $j= new Job();
    $j->setTitle('New job');
    $j->setValid_from(Date::now());
    $j->setExpire_at(null);

    $id= $j->insert();
    $this->assertEquals(14121977, $id);
  }
  
  #[@test]
  public function saveReturnsIdentityForInserts() {
    $mock= $this->getConnection();
    $mock->setIdentityValue(14121977);

    $j= new Job();
    $j->setTitle('New job');
    $j->setValid_from(Date::now());
    $j->setExpire_at(null);

    $id= $j->save();
    $this->assertEquals(14121977, $id);
  }

  #[@test]
  public function saveReturnsIdentityForUpdates() {
    $this->setResults(new MOckResultSet(array(
      0 => array(   // First row
        'job_id'      => 1,
        'title'       => 'Unit tester',
        'valid_from'  => Date::now(),
        'expire_at'   => null
      )
    )));
    
    $job= Job::getByJob_id(1);
    $this->assertNotEquals(null, $job);
    $id= $job->save();
    $this->assertEquals(1, $id);
  }
  
  #[@test]
  public function identityFieldIsSet() {
    $mock= $this->getConnection();
    $mock->setIdentityValue(14121977);

    $j= new Job();
    $j->setTitle('New job');
    $j->setValid_from(Date::now());
    $j->setExpire_at(null);

    $this->assertEquals(0, $j->getJob_id());

    $j->insert();
    $this->assertEquals(14121977, $j->getJob_id());
  }
  
  #[@test, @expect('rdbms.SQLException')]
  public function failedQueryInInsert() {
    $mock= $this->getConnection();
    $mock->makeQueryFail(1205, 'Deadlock');

    $j= new Job();
    $j->setTitle('New job');
    $j->setValid_from(Date::now());
    $j->setExpire_at(null);

    $j->insert();
  }
  
  #[@test]
  public function oneResultForDoSelect() {
    $this->setResults(new MOckResultSet(array(
      0 => array(
        'job_id'      => 1,
        'title'       => 'Unit tester',
        'valid_from'  => Date::now(),
        'expire_at'   => null
      )
    )));
  
    $peer= Job::getPeer();
    $jobs= $peer->doSelect(new \rdbms\Criteria(array('title', 'Unit tester', EQUAL)));

    $this->assertArray($jobs);
    $this->assertEquals(1, sizeof($jobs));
    $this->assertClass($jobs[0], 'net.xp_framework.unittest.rdbms.dataset.Job');
  }

  #[@test]
  public function noResultForDoSelect() {
    $this->setResults(new MOckResultSet());
  
    $peer= Job::getPeer();
    $jobs= $peer->doSelect(new \rdbms\Criteria(array('job_id', self::IRRELEVANT_NUMBER, EQUAL)));

    $this->assertArray($jobs);
    $this->assertEquals(0, sizeof($jobs));
  }

  #[@test]
  public function multipleResultForDoSelect() {
    $this->setResults(new MOckResultSet(array(
      0 => array(
        'job_id'      => 1,
        'title'       => 'Unit tester',
        'valid_from'  => Date::now(),
        'expire_at'   => null
      ),
      1 => array(
        'job_id'      => 9,
        'title'       => 'PHP programmer',
        'valid_from'  => Date::now(),
        'expire_at'   => DateUtil::addDays(Date::now(), 7)
      )
    )));
  
    $peer= Job::getPeer();
    $jobs= $peer->doSelect(new \rdbms\Criteria(array('job_id', 10, LESS_THAN)));

    $this->assertArray($jobs);
    $this->assertEquals(2, sizeof($jobs));
    $this->assertClass($jobs[0], 'net.xp_framework.unittest.rdbms.dataset.Job');
    $this->assertEquals(1, $jobs[0]->getJob_id());
    $this->assertClass($jobs[1], 'net.xp_framework.unittest.rdbms.dataset.Job');
    $this->assertEquals(9, $jobs[1]->getJob_id());
  }
  
  #[@test]
  public function iterateOverCriteria() {
    $this->setResults(new MOckResultSet(array(
      0 => array(
        'job_id'      => 654,
        'title'       => 'Java Unit tester',
        'valid_from'  => Date::now(),
        'expire_at'   => null
      ),
      1 => array(
        'job_id'      => 329,
        'title'       => 'C# programmer',
        'valid_from'  => Date::now(),
        'expire_at'   => null
      )
    )));

    $peer= Job::getPeer();
    $iterator= $peer->iteratorFor(new \rdbms\Criteria(array('expire_at', null, EQUAL)));

    $this->assertClass($iterator, 'rdbms.ResultIterator');
    
    // Make sure hasNext() does not forward the resultset pointer
    $this->assertTrue($iterator->hasNext());
    $this->assertTrue($iterator->hasNext());
    $this->assertTrue($iterator->hasNext());
    
    $job= $iterator->next();
    $this->assertClass($job, 'net.xp_framework.unittest.rdbms.dataset.Job');
    $this->assertEquals(654, $job->getJob_id());

    $this->assertTrue($iterator->hasNext());

    $job= $iterator->next();
    $this->assertClass($job, 'net.xp_framework.unittest.rdbms.dataset.Job');
    $this->assertEquals(329, $job->getJob_id());

    $this->assertFalse($iterator->hasNext());
  }

  #[@test]
  public function nextCallWithoutHasNext() {
    $this->setResults(new MOckResultSet(array(
      0 => array(
        'job_id'      => 654,
        'title'       => 'Java Unit tester',
        'valid_from'  => Date::now(),
        'expire_at'   => null
      ),
      1 => array(
        'job_id'      => 329,
        'title'       => 'C# programmer',
        'valid_from'  => Date::now(),
        'expire_at'   => null
      )
    )));

    $peer= Job::getPeer();
    $iterator= $peer->iteratorFor(new \rdbms\Criteria(array('expire_at', null, EQUAL)));

    $job= $iterator->next();
    $this->assertClass($job, 'net.xp_framework.unittest.rdbms.dataset.Job');
    $this->assertEquals(654, $job->getJob_id());

    $this->assertTrue($iterator->hasNext());
  }

  #[@test, @expect('util.NoSuchElementException')]
  public function nextCallOnEmptyResultSet() {
    $this->setResults(new MOckResultSet());
    $peer= Job::getPeer();
    $iterator= $peer->iteratorFor(new \rdbms\Criteria(array('expire_at', null, EQUAL)));
    $iterator->next();
  }

  #[@test, @expect('util.NoSuchElementException')]
  public function nextCallPastEndOfResultSet() {
    $this->setResults(new MOckResultSet(array(
      0 => array(
        'job_id'      => 654,
        'title'       => 'Java Unit tester',
        'valid_from'  => Date::now(),
        'expire_at'   => null
      )
    )));

    $peer= Job::getPeer();
    $iterator= $peer->iteratorFor(new \rdbms\Criteria(array('expire_at', null, EQUAL)));
    $iterator->next();
    $iterator->next();
  }
  
  #[@test]
  public function iterateOverStatement() {
    $this->setResults(new MOckResultSet(array(
      0 => array(
        'job_id'      => 654,
        'title'       => 'Java Unit tester',
        'valid_from'  => Date::now(),
        'expire_at'   => null
      )
    )));

    $peer= Job::getPeer();
    $iterator= $peer->iteratorFor(new Statement('select object(j) from job j where 1 = 1'));
    $this->assertClass($iterator, 'rdbms.ResultIterator');

    $this->assertTrue($iterator->hasNext());

    $job= $iterator->next();
    $this->assertClass($job, 'net.xp_framework.unittest.rdbms.dataset.Job');
    $this->assertEquals(654, $job->getJob_id());
    $this->assertEquals('Java Unit tester', $job->getTitle());

    $this->assertFalse($iterator->hasNext());
  }

  #[@test]
  public function updateUnchangedObject() {

    // First, retrieve an object
    $this->setResults(new MOckResultSet(array(
      0 => array(
        'job_id'      => 654,
        'title'       => 'Java Unit tester',
        'valid_from'  => Date::now(),
        'expire_at'   => null
      )
    )));
    $job= Job::getByJob_id(1);
    $this->assertNotEquals(null, $job);

    // Second, update the job. Make the next query fail on this 
    // connection to ensure that nothing is actually done.
    $mock= $this->getConnection();
    $mock->makeQueryFail(1326, 'Syntax error');
    $job->update();

    // Make next query return empty results (not fail)
    $this->setResults(new MOckResultSet());
  }

  #[@test]
  public function column() {
    $c= Job::column('job_id');
    $this->assertClass($c, 'rdbms.Column');
    $this->assertEquals('job_id', $c->getName());
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function nonExistantColumn() {
    Job::column('non_existant');
  }

  #[@test]
  public function relativeColumn() {
    $this->assertClass(Job::column('PersonJob->person_id'), 'rdbms.Column');
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function nonExistantRelativeColumn() {
    Job::column('PersonJob->non_existant');
  }

  #[@test]
  public function farRelativeColumn() {
    $this->assertClass(Job::column('PersonJob->Department->department_id'), 'rdbms.Column');
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function nonExistantfarRelativeColumn() {
    Job::column('PersonJob->Department->non_existant');
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function nonExistantRelative() {
    Job::column('NonExistant->person_id');
  }


  #[@test]
  public function doUpdate() {
    $this->setResults(new MOckResultSet(array(
      0 => array(
        'job_id'      => 654,
        'title'       => 'Java Unit tester',
        'valid_from'  => Date::now(),
        'expire_at'   => null
      )
    )));
    $job= Job::getByJob_id(654);
    $this->assertNotEquals(null, $job);
    $job->setTitle('PHP Unit tester');
    $job->doUpdate(new \rdbms\Criteria(array('job_id', $job->getJob_id(), EQUAL)));
  }

  #[@test]
  public function doDelete() {
    $this->setResults(new MOckResultSet(array(
      0 => array(
        'job_id'      => 654,
        'title'       => 'Java Unit tester',
        'valid_from'  => Date::now(),
        'expire_at'   => null
      )
    )));
    $job= Job::getByJob_id(654);
    $this->assertNotEquals(null, $job);
    $job->doDelete(new \rdbms\Criteria(array('job_id', $job->getJob_id(), EQUAL)));
  }

  #[@test]
  public function percentSign() {
    $observer= $this->getConnection()->addObserver(newinstance('rdbms.DBObserver', array(create('new Vector<lang.types.String>')), '{
      public $statements;
      public function __construct($statements) {
        $this->statements= $statements;
      }
      public static function instanceFor($arg) { }
      public function update($observable, $event= NULL) {
        if ($event instanceof DBEvent && "query" == $event->getName()) {
          $this->statements[]= new String($event->getArgument());
        }
      }
    }'));
    $j= new Job();
    $j->setTitle('Percent%20Sign');
    $j->insert();
    
    $this->assertEquals(
      new String('insert into JOBS.job (title) values ("Percent%20Sign")'),
      $observer->statements[0]
    );
  }

  #[@test]
  public function testDoSelectMax() {
    for ($i= 0; $i < 4; $i++) {
      $this->setResults(new MockResultSet(array(
        0 => array(
          'job_id'      => 654,
          'title'       => 'Java Unit tester',
          'valid_from'  => Date::now(),
          'expire_at'   => null
        ),
        1 => array(
          'job_id'      => 655,
          'title'       => 'Java Unit tester 1',
          'valid_from'  => Date::now(),
          'expire_at'   => null
        ),
        2 => array(
          'job_id'      => 656,
          'title'       => 'Java Unit tester 2',
          'valid_from'  => Date::now(),
          'expire_at'   => null
        ),
        3 => array(
          'job_id'      => 657,
          'title'       => 'Java Unit tester 3',
          'valid_from'  => Date::now(),
          'expire_at'   => null
        ),
      )));
      $this->assertEquals($i ? $i : 4, count(Job::getPeer()->doSelect(new \rdbms\Criteria(), $i)));
    }
  }
}
