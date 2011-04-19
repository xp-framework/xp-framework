<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'text.csv.CsvListReader',
    'text.csv.processors.lookup.GetDataSet',
    'text.csv.processors.lookup.FindDataSet',
    'net.xp_framework.unittest.rdbms.dataset.Job',
    'net.xp_framework.unittest.rdbms.dataset.JobFinder',
    'net.xp_framework.unittest.rdbms.mock.MockConnection',
    'io.streams.MemoryInputStream'
  );

  /**
   * TestCase
   *
   * @see      xp://text.csv.CellProcessor
   */
  class DataSetCellProcessorTest extends TestCase {

    /**
     * Mock connection registration
     *
     */  
    #[@beforeClass]
    public static function registerMockConnection() {
      DriverManager::register('mock', XPClass::forName('net.xp_framework.unittest.rdbms.mock.MockConnection'));
      Job::getPeer()->setConnection(DriverManager::getConnection('mock://mock/JOBS?autoconnect=1'));
    }

    /**
     * Creates a new list reader
     *
     * @param   string str
     * @param   text.csv.CsvFormat format
     * @return  text.csv.CsvListReader
     */
    protected function newReader($str, CsvFormat $format= NULL) {
      return new CsvListReader(new TextReader(new MemoryInputStream($str)), $format);
    }
  
    /**
     * Test successful lookup
     *
     */
    #[@test]
    public function getByPrimary() {
      Job::getPeer()->getConnection()->setResultSet(new MockResultSet(array(
        array('job_id' => 1549, 'title' => 'Developer')
      )));
      $in= $this->newReader("job_id;title\n1549;10248")->withProcessors(array(
        new GetDataSet(create(new JobFinder())->method('byPrimary')),
        NULL
      ));
      $in->getHeaders();
      $list= $in->read();
      $this->assertClass($list[0], 'net.xp_framework.unittest.rdbms.dataset.Job');
      $this->assertEquals(1549, $list[0]->getJob_id());
    }

    /**
     * Test successful lookup
     *
     */
    #[@test]
    public function findByTitle() {
      Job::getPeer()->getConnection()->setResultSet(new MockResultSet(array(
        array('job_id' => 1549, 'title' => 'Developer')
      )));
      $in= $this->newReader("title;external_id\nDeveloper;10248")->withProcessors(array(
        new GetDataSet(create(new JobFinder())->method('similarTo')),
        NULL
      ));
      $in->getHeaders();
      $list= $in->read();
      $this->assertClass($list[0], 'net.xp_framework.unittest.rdbms.dataset.Job');
      $this->assertEquals(1549, $list[0]->getJob_id());
    }

    /**
     * Test lookup not returning a result
     *
     */
    #[@test]
    public function getNotFound() {
      Job::getPeer()->getConnection()->setResultSet(new MockResultSet(array()));
      $in= $this->newReader("job_id;title\n1549;Developer")->withProcessors(array(
        new GetDataSet(create(new JobFinder())->method('byPrimary')),
        NULL
      ));
      $in->getHeaders();
      try {
        $in->read();
        $this->fail('Lookup succeeded', NULL, 'lang.FormatException');
      } catch (FormatException $expected) { }
    }

    /**
     * Test lookup not returning a result
     *
     */
    #[@test]
    public function findNotFound() {
      Job::getPeer()->getConnection()->setResultSet(new MockResultSet(array()));
      $in= $this->newReader("job_id;title\n1549;Developer")->withProcessors(array(
        new FindDataSet(create(new JobFinder())->method('byPrimary')),
        NULL
      ));
      $in->getHeaders();
      $list= $in->read();
      $this->assertNull($list[0]);
    }

    /**
     * Test lookup returning more than one result
     *
     */
    #[@test]
    public function ambiguous() {
      Job::getPeer()->getConnection()->setResultSet(new MockResultSet(array(
        array('job_id' => 1549, 'title' => 'Developer'),
        array('job_id' => 1549, 'title' => 'Doppelgänger'),
      )));
      $in= $this->newReader("job_id;title\n1549;10248")->withProcessors(array(
        new GetDataSet(create(new JobFinder())->method('byPrimary')),
        NULL
      ));
      $in->getHeaders();
      try {
        $in->read();
        $this->fail('Lookup succeeded', NULL, 'lang.FormatException');
      } catch (FormatException $expected) { }
    }
  }
?>
