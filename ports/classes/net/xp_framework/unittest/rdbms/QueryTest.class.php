<?php
/* This class is part of the XP framework
 *
 * $Id: CriteriaTest.class.php 9319 2007-01-17 15:07:44Z friebe $ 
 */
 
  uses(
    'unittest.TestCase',
    'rdbms.Query',
    'rdbms.Criteria',
    'net.xp_framework.unittest.rdbms.mock.MockConnection',
    'net.xp_framework.unittest.rdbms.dataset.Job'
  );

  define('MOCK_CONNECTION_CLASS', 'net.xp_framework.unittest.rdbms.mock.MockConnection');

  /**
   * Test query class
   *
   * @see      xp://rdbms.Query
   * @purpose  Unit Test
   */
  class QueryTest extends TestCase {
      
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
     * Test existance
     *
     */
    #[@test]
    public function newQuery() {
      $this->assertTrue(class_exists('Query'));
    }
    
    /**
     * set and store mode
     *
     */
    #[@test]
    public function setMode() {
      $q= new Query();
      $q->setMode(Query::INSERT);
      $this->assertEquals(Query::INSERT, $q->getMode());
    }
    
    /**
     * set and store criteria
     *
     */
    #[@test]
    public function setCriteria() {
      $q= new Query();
      $c= new Criteria();
      $q->setCriteria($c);
      $this->assertEquals($c, $q->getCriteria());
    }
    
    /**
     * set and store Dataset by name
     *
     */
    #[@test]
    public function setPeer() {
      $q= new Query();
      $q->setPeer(Job::getPeer());
      $this->assertEquals(Job::getPeer(), $q->getPeer());
    }
    
    /**
     * get Connection
     *
     */
    #[@test]
    public function getConnection() {
      $q= new Query();
      $q->setPeer(Job::getPeer());
      $this->assertClass($q->getConnection(), 'net.xp_framework.unittest.rdbms.mock.MockConnection');
    }
    
    /**
     * set invalid mode
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function setInvalidMode() {
      create(new Query())->setMode('BAD_MODE');
    }
    
    /**
     * set invalid mode
     *
     */
    #[@test, @expect('lang.IllegalStateException')]
    public function executeWithoutMode() {
      create(new Query())->execute();
    }
    
    /**
     * withRestriction test
     *
     */
    #[@test]
    public function executeWithRestriction() {
      $this->assertClass(create(new Query())->withRestriction(Job::column('job_id')->equal(5)), 'rdbms.Query');
    }
    
  }
?>
