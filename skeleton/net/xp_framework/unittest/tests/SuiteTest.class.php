<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses('util.profiling.unittest.TestCase', 'net.xp_framework.unittest.tests.SimpleTestCase');

  /**
   * Test TestSuite class methods
   *
   * @see      xp://util.profiling.unittest.TestSuite
   * @purpose  Unit Test
   */
  class SuiteTest extends TestCase {
    var
      $suite= NULL;
      
    /**
     * Setup method. Creates a new test suite.
     *
     * @access  public
     */
    function setUp() {
      $this->suite= &new TestSuite();
    }

    /**
     * Tests a test suite is initially empty
     *
     * @access  public
     */    
    #[@test]
    function initallyEmpty() {
      $this->assertEquals(0, $this->suite->numTests());
    }    

    /**
     * Tests adding a test
     *
     * @access  public
     */    
    #[@test]
    function addingATest() {
      $this->suite->addTest($this);
      $this->assertEquals(1, $this->suite->numTests());
    }    

    /**
     * Tests adding a test
     *
     * @access  public
     */    
    #[@test, @expect('lang.IllegalArgumentException')]
    function addNonTest() {
      $this->suite->addTest(new Object());
    }    

    /**
     * Tests clearing tests
     *
     * @access  public
     */    
    #[@test]
    function clearingTests() {
      $this->suite->addTest($this);
      $this->assertEquals(1, $this->suite->numTests());
      $this->suite->clearTests();
      $this->assertEquals(0, $this->suite->numTests());
    }
    
    /**
     * Tests running a single test
     *
     * @access  public
     */    
    #[@test]
    function runningASingleSucceedingTest() {
      $r= &$this->suite->runTest(new SimpleTestCase('succeeds'));
      $this->assertClass($r, 'util.profiling.unittest.TestResult') &&
      $this->assertEquals(1, $r->runCount(), 'runCount') &&
      $this->assertEquals(1, $r->successCount(), 'successCount') &&
      $this->assertEquals(0, $r->failureCount(), 'failureCount') &&
      $this->assertEquals(0, $r->skipCount(), 'skipCount');
    }    

    /**
     * Tests running a single test
     *
     * @access  public
     */    
    #[@test]
    function runningASingleFailingTest() {
      $r= &$this->suite->runTest(new SimpleTestCase('fails'));
      $this->assertClass($r, 'util.profiling.unittest.TestResult') &&
      $this->assertEquals(1, $r->runCount(), 'runCount') &&
      $this->assertEquals(0, $r->successCount(), 'successCount') &&
      $this->assertEquals(1, $r->failureCount(), 'failureCount') &&
      $this->assertEquals(0, $r->skipCount(), 'skipCount');
    }    
  }
?>
