<?php
/* This class is part of the XP framework
 *
 * $Id: DataSetTest.class.php 9290 2007-01-15 21:04:55Z friebe $ 
 */

  uses(
    'unittest.TestCase',
    'rdbms.DriverManager',
    'rdbms.ConnectionManager',
    'util.Date',
    'util.DateUtil',
    'rdbms.Statement',
    'net.xp_framework.unittest.rdbms.mock.MockConnection',
    'net.xp_framework.unittest.rdbms.dataset.Job'
  );
  
  define('MOCK_CONNECTION_CLASS', 'net.xp_framework.unittest.rdbms.mock.MockConnection');
  define('IRRELEVANT_NUMBER',     -1);

  /**
   * O/R-mapping API unit test
   *
   * @see      xp://rdbms.DataSet
   * @purpose  TestCase
   */
  class DataSetTest extends TestCase {

    /**
     * Static initializer
     *
     */  
    public static function __static() {
      DriverManager::register('mock', XPClass::forName(MOCK_CONNECTION_CLASS));
    }
    
    /**
     * Setup method
     *
     */
    public function setUp() {
      ConnectionManager::getInstance()->register(
        DriverManager::getConnection('mock://mock/JOBS?autoconnect=1'), 
        'jobs'
      );
    }
    
    /**
     * Helper methods
     *
     * @return  &net.xp_framework.unittest.rdbms.mock.MockConnection
     */
    protected function getConnection() {
      return ConnectionManager::getInstance()->getByHost('jobs', 0);
    }
    
    /**
     * Helper method
     *
     * @param   &net.xp_framework.unittest.rdbms.mock.MockResultSet r
     */
    protected function setResults($r) {
      $this->getConnection()->setResultSet($r);
    }
    
    /**
     * Tests the getPeer() method
     *
     */
    #[@test]
    public function peerObject() {
      $peer= Job::getPeer();
      $this->assertClass($peer, 'rdbms.Peer');
      $this->assertEquals('job', strtolower($peer->identifier));
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
    
    /**
     * Tests the getByJob_id() method
     *
     */
    #[@test]
    public function getByJob_id() {
      $now= Date::now();
      $this->setResults(new MockResultSet(array(
        0 => array(   // First row
          'job_id'      => 1,
          'title'       => 'Unit tester',
          'valid_from'  => $now,
          'expire_at'   => NULL
        )
      )));
      
      $job= Job::getByJob_id(1);
      $this->assertClass($job, 'net.xp_framework.unittest.rdbms.dataset.Job');
      $this->assertEquals(1, $job->getJob_id());
      $this->assertEquals('Unit tester', $job->getTitle());
      $this->assertEquals($now, $job->getValid_from());
      $this->assertNull($job->getExpire_at());
    }
    
    /**
     * Tests the isNew() method when creating a job object by means of new()
     *
     */
    #[@test]
    public function newObject() {
      $j= new Job();
      $this->assertTrue($j->isNew());
    }

    /**
     * Tests the isNew() method when fetching the object by getByJob_id()
     *
     */
    #[@test]
    public function existingObject() {
      $this->setResults(new MockResultSet(array(
        0 => array(   // First row
          'job_id'      => 1,
          'title'       => 'Unit tester',
          'valid_from'  => $now,
          'expire_at'   => NULL
        )
      )));
      
      $job= Job::getByJob_id(1);
      $this->assertFalse($job->isNew());
    }

    /**
     * Tests the isNew() method after saving an object
     *
     */
    #[@test]
    public function noLongerNewAfterSave() {
      $j= new Job();
      $j->setTitle('New job');
      $j->setValid_from(Date::now());
      $j->setExpire_at(NULL);
      
      $this->assertTrue($j->isNew());
      $j->save();
      $this->assertFalse($j->isNew());
    }

    /**
     * Tests that getByJob_id() method returns NULL if nothing is found
     *
     */
    #[@test]
    public function noResultsDuringGetByJob_id() {
      $this->setResults(new MockResultSet());
      $this->assertNull(Job::getByJob_id(IRRELEVANT_NUMBER));
    }

    /**
     * Tests that getByJob_id() method will throw an exception if the SQL
     * query fails
     *
     */
    #[@test, @expect('rdbms.SQLException')]
    public function failedQueryInGetByJob_id() {
      $mock= $this->getConnection();
      $mock->makeQueryFail(1, 'Select failed');

      Job::getByJob_id(IRRELEVANT_NUMBER);
    }

    /**
     * Tests that the insert() method will return the identity value
     *
     * @see     xp://rdbms.DataSet#insert
     */
    #[@test]
    public function insertReturnsIdentity() {
      $mock= $this->getConnection();
      $mock->setIdentityValue(14121977);

      $j= new Job();
      $j->setTitle('New job');
      $j->setValid_from(Date::now());
      $j->setExpire_at(NULL);

      $id= $j->insert();
      $this->assertEquals(14121977, $id);
    }
    
    /**
     * Tests that the save() method will return the identity value
     *
     * @see     xp://rdbms.DataSet#insert
     */
    #[@test]
    public function saveReturnsIdentityForInserts() {
      $mock= $this->getConnection();
      $mock->setIdentityValue(14121977);

      $j= new Job();
      $j->setTitle('New job');
      $j->setValid_from(Date::now());
      $j->setExpire_at(NULL);

      $id= $j->save();
      $this->assertEquals(14121977, $id);
    }

    /**
     * Tests that the save() method will return the identity value
     *
     * @see     xp://rdbms.DataSet#insert
     */
    #[@test]
    public function saveReturnsIdentityForUpdates() {
      $this->setResults(new MockResultSet(array(
        0 => array(   // First row
          'job_id'      => 1,
          'title'       => 'Unit tester',
          'valid_from'  => $now,
          'expire_at'   => NULL
        )
      )));
      
      $job= Job::getByJob_id(1);
      $id= $job->save();
      $this->assertEquals(1, $id);
    }
    
    /**
     * Tests that the insert() method will set the identity field's value
     * and that it is set to its initial value before.
     *
     * @see     xp://rdbms.DataSet#insert
     */
    #[@test]
    public function identityFieldIsSet() {
      $mock= $this->getConnection();
      $mock->setIdentityValue(14121977);

      $j= new Job();
      $j->setTitle('New job');
      $j->setValid_from(Date::now());
      $j->setExpire_at(NULL);

      $this->assertEquals(0, $j->getJob_id());

      $j->insert();
      $this->assertEquals(14121977, $j->getJob_id());
    }
    
    /**
     * Tests that the insert() method will throw an exception in case the
     * SQL query fails
     *
     * @see     xp://rdbms.DataSet#insert
     */
    #[@test, @expect('rdbms.SQLException')]
    public function failedQueryInInsert() {
      $mock= $this->getConnection();
      $mock->makeQueryFail(1205, 'Deadlock');

      $j= new Job();
      $j->setTitle('New job');
      $j->setValid_from(Date::now());
      $j->setExpire_at(NULL);

      $j->insert();
    }
    
    /**
     * Tests that the doSelect() will return an array of objects
     *
     */
    #[@test]
    public function oneResultForDoSelect() {
      $this->setResults(new MockResultSet(array(
        0 => array(
          'job_id'      => 1,
          'title'       => 'Unit tester',
          'valid_from'  => Date::now(),
          'expire_at'   => NULL
        )
      )));
    
      $peer= Job::getPeer();
      $jobs= $peer->doSelect(new Criteria(array('title', 'Unit tester', EQUAL)));

      $this->assertArray($jobs);
      $this->assertEquals(1, sizeof($jobs));
      $this->assertClass($jobs[0], 'net.xp_framework.unittest.rdbms.dataset.Job');
    }

    /**
     * Tests that the doSelect() will return an empty array if nothing is found
     *
     */
    #[@test]
    public function noResultForDoSelect() {
      $this->setResults(new MockResultSet());
    
      $peer= Job::getPeer();
      $jobs= $peer->doSelect(new Criteria(array('job_id', IRRELEVANT_NUMBER, EQUAL)));

      $this->assertArray($jobs);
      $this->assertEquals(0, sizeof($jobs));
    }

    /**
     * Tests that the doSelect() will return an array of objects
     *
     */
    #[@test]
    public function multipleResultForDoSelect() {
      $this->setResults(new MockResultSet(array(
        0 => array(
          'job_id'      => 1,
          'title'       => 'Unit tester',
          'valid_from'  => Date::now(),
          'expire_at'   => NULL
        ),
        1 => array(
          'job_id'      => 9,
          'title'       => 'PHP programmer',
          'valid_from'  => Date::now(),
          'expire_at'   => DateUtil::addDays(Date::now(), 7)
        )
      )));
    
      $peer= Job::getPeer();
      $jobs= $peer->doSelect(new Criteria(array('job_id', 10, LESS_THAN)));

      $this->assertArray($jobs);
      $this->assertEquals(2, sizeof($jobs));
      $this->assertClass($jobs[0], 'net.xp_framework.unittest.rdbms.dataset.Job');
      $this->assertEquals(1, $jobs[0]->getJob_id());
      $this->assertClass($jobs[1], 'net.xp_framework.unittest.rdbms.dataset.Job');
      $this->assertEquals(9, $jobs[1]->getJob_id());
    }
    
    /**
     * Tests the iteratorFor() method with criteria
     *
     */
    #[@test]
    public function iterateOverCriteria() {
      $this->setResults(new MockResultSet(array(
        0 => array(
          'job_id'      => 654,
          'title'       => 'Java Unit tester',
          'valid_from'  => Date::now(),
          'expire_at'   => NULL
        ),
        1 => array(
          'job_id'      => 329,
          'title'       => 'C# programmer',
          'valid_from'  => Date::now(),
          'expire_at'   => NULL
        )
      )));

      $peer= Job::getPeer();
      $iterator= $peer->iteratorFor(new Criteria(array('expire_at', NULL, EQUAL)));

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

    /**
     * Tests that ResultIterator::next() can be called without previously having
     * called hasMext()
     *
     */
    #[@test]
    public function nextCallWithoutHasNext() {
      $this->setResults(new MockResultSet(array(
        0 => array(
          'job_id'      => 654,
          'title'       => 'Java Unit tester',
          'valid_from'  => Date::now(),
          'expire_at'   => NULL
        ),
        1 => array(
          'job_id'      => 329,
          'title'       => 'C# programmer',
          'valid_from'  => Date::now(),
          'expire_at'   => NULL
        )
      )));

      $peer= Job::getPeer();
      $iterator= $peer->iteratorFor(new Criteria(array('expire_at', NULL, EQUAL)));

      $job= $iterator->next();
      $this->assertClass($job, 'net.xp_framework.unittest.rdbms.dataset.Job');
      $this->assertEquals(654, $job->getJob_id());

      $this->assertTrue($iterator->hasNext());
    }

    /**
     * Tests that ResultIterator::next() will throw an exception in case it
     * is called on an empty resultset.
     *
     */
    #[@test, @expect('util.NoSuchElementException')]
    public function nextCallOnEmptyResultSet() {
      $this->setResults(new MockResultSet());
      $peer= Job::getPeer();
      $iterator= $peer->iteratorFor(new Criteria(array('expire_at', NULL, EQUAL)));
      $iterator->next();
    }

    /**
     * Tests that ResultIterator::next() will throw an exception in case it
     * has iterated past the end of a resultset.
     *
     */
    #[@test, @expect('util.NoSuchElementException')]
    public function nextCallPastEndOfResultSet() {
      $this->setResults(new MockResultSet(array(
        0 => array(
          'job_id'      => 654,
          'title'       => 'Java Unit tester',
          'valid_from'  => Date::now(),
          'expire_at'   => NULL
        )
      )));

      $peer= Job::getPeer();
      $iterator= $peer->iteratorFor(new Criteria(array('expire_at', NULL, EQUAL)));
      $iterator->next();
      $iterator->next();
    }
    
    /**
     * Tests the iteratorFor() method with statement
     *
     */
    #[@test]
    public function iterateOverStatement() {
      $this->setResults(new MockResultSet(array(
        0 => array(
          'job_id'      => 654,
          'title'       => 'Java Unit tester',
          'valid_from'  => Date::now(),
          'expire_at'   => NULL
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

    /**
     * Tests that update doesn't do anything when the object is unchanged
     *
     */
    #[@test]
    public function updateUnchangedObject() {

      // First, retrieve an object
      $this->setResults(new MockResultSet(array(
        0 => array(
          'job_id'      => 654,
          'title'       => 'Java Unit tester',
          'valid_from'  => Date::now(),
          'expire_at'   => NULL
        )
      )));
      $job= Job::getByJob_id(1);

      // Second, update the job. Make the next query fail on this 
      // connection to ensure that nothing is actually done.
      $mock= $this->getConnection();
      $mock->makeQueryFail(1326, 'Syntax error');
      $job->update();

      // Make next query return empty results (not fail)
      $this->setResults(new MockResultSet());
    }
  }
?>
