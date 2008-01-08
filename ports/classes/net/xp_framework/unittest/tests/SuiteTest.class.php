<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'unittest.TestCase',
    'unittest.TestSuite',
    'net.xp_framework.unittest.tests.SimpleTestCase',
    'net.xp_framework.unittest.tests.AnotherTestCase'
  );

  /**
   * Test TestSuite class methods
   *
   * @see      xp://unittest.TestSuite
   * @purpose  Unit Test
   */
  class SuiteTest extends TestCase {
    public
      $suite= NULL;
      
    /**
     * Setup method. Creates a new test suite.
     *
     */
    public function setUp() {
      $this->suite= new TestSuite();
    }

    /**
     * Tests a test suite is initially empty
     *
     */    
    #[@test]
    public function initallyEmpty() {
      $this->assertEquals(0, $this->suite->numTests());
    }    

    /**
     * Tests adding a test
     *
     */    
    #[@test]
    public function addingATest() {
      $this->suite->addTest($this);
      $this->assertEquals(1, $this->suite->numTests());
    }    

    /**
     * Tests adding a test
     *
     */    
    #[@test, @expect('lang.IllegalArgumentException')]
    public function addNonTest() {
      $this->suite->addTest(new Object());
    }

    /**
     * Tests adding a test
     *
     */    
    #[@test, @expect('lang.IllegalArgumentException')]
    public function runNonTest() {
      $this->suite->runTest(new Object());
    }

    /**
     * Tests adding an invalid test
     *
     */    
    #[@test, @expect('lang.MethodNotImplementedException')]
    public function addInvalidTest() {
      $this->suite->addTest(newinstance('unittest.TestCase', array('nonExistant'), '{}'));
    }

    /**
     * Tests adding an invalid test
     *
     */    
    #[@test, @expect('lang.MethodNotImplementedException')]
    public function runInvalidTest() {
      $this->suite->runTest(newinstance('unittest.TestCase', array('nonExistant'), '{}'));
    }

    /**
     * Tests adding a test class
     *
     */    
    #[@test]
    public function addingATestClass() {
      $ignored= $this->suite->addTestClass(XPClass::forName('net.xp_framework.unittest.tests.SimpleTestCase'));
      $this->assertNotEmpty($ignored);
      for ($i= 0, $s= $this->suite->numTests(); $i < $s; $i++) {
        $this->assertSubclass($this->suite->testAt($i), 'unittest.TestCase');
      }
    }    

    /**
     * Tests adding a test class without tests inside
     *
     */    
    #[@test, @expect('util.NoSuchElementException')]
    public function addingEmptyTest() {
      $this->suite->addTestClass(XPClass::forName('net.xp_framework.unittest.tests.EmptyTestCase'));
    }    

    /**
     * Tests adding a test class
     *
     */    
    #[@test]
    public function addingEmptyTestAfter() {
      $this->suite->addTestClass(XPClass::forName('net.xp_framework.unittest.tests.SimpleTestCase'));
      $before= $this->suite->numTests();
      try {
        $this->suite->addTestClass(XPClass::forName('net.xp_framework.unittest.tests.EmptyTestCase'));
        $this->fail('Expected exception not thrown', NULL, 'util.NoSuchElementException');
      } catch (NoSuchElementException $expected) { 
      }
      $this->assertEquals($before, $this->suite->numTests());
    }

    /**
     * Tests adding a test class
     *
     */    
    #[@test, @expect('lang.IllegalArgumentException')]
    public function addingANonTestClass() {
      $this->suite->addTestClass(XPClass::forName('lang.Object'));
    }    

    /**
     * Tests clearing tests
     *
     */    
    #[@test]
    public function clearingTests() {
      $this->suite->addTest($this);
      $this->assertEquals(1, $this->suite->numTests());
      $this->suite->clearTests();
      $this->assertEquals(0, $this->suite->numTests());
    }
    
    /**
     * Tests running a single test
     *
     */    
    #[@test]
    public function runningASingleSucceedingTest() {
      $r= $this->suite->runTest(new SimpleTestCase('succeeds'));
      $this->assertClass($r, 'unittest.TestResult');
      $this->assertEquals(1, $r->count(), 'count');
      $this->assertEquals(1, $r->runCount(), 'runCount');
      $this->assertEquals(1, $r->successCount(), 'successCount');
      $this->assertEquals(0, $r->failureCount(), 'failureCount');
      $this->assertEquals(0, $r->skipCount(), 'skipCount');
    }    

    /**
     * Tests running a single test
     *
     */    
    #[@test]
    public function runningASingleFailingTest() {
      $r= $this->suite->runTest(new SimpleTestCase('fails'));
      $this->assertClass($r, 'unittest.TestResult');
      $this->assertEquals(1, $r->count(), 'count');
      $this->assertEquals(1, $r->runCount(), 'runCount');
      $this->assertEquals(0, $r->successCount(), 'successCount');
      $this->assertEquals(1, $r->failureCount(), 'failureCount');
      $this->assertEquals(0, $r->skipCount(), 'skipCount');
    }    

    /**
     * Tests running multiple tests
     *
     */    
    #[@test]
    public function runMultipleTests() {
      $this->suite->addTest(new SimpleTestCase('fails'));
      $this->suite->addTest(new SimpleTestCase('succeeds'));
      $this->suite->addTest(new SimpleTestCase('skipped'));
      $this->suite->addTest(new SimpleTestCase('ignored'));
      $r= $this->suite->run();
      $this->assertClass($r, 'unittest.TestResult');
      $this->assertEquals(4, $r->count(), 'count');
      $this->assertEquals(2, $r->runCount(), 'runCount');
      $this->assertEquals(1, $r->successCount(), 'successCount');
      $this->assertEquals(1, $r->failureCount(), 'failureCount');
      $this->assertEquals(2, $r->skipCount(), 'skipCount');
    }    

    /**
     * Tests method decorated with beforeClass is executed
     *
     */    
    #[@test]
    public function runInvokesBeforeClass() {
      SimpleTestCase::$init= 0;
      $this->suite->addTest(new SimpleTestCase('fails'));
      $this->suite->addTest(new SimpleTestCase('succeeds'));
      $this->suite->run();
      $this->assertEquals(1, SimpleTestCase::$init);
    }    

    /**
     * Tests method decorated with beforeClass is executed
     *
     */    
    #[@test]
    public function runTestInvokesBeforeClass() {
      SimpleTestCase::$init= 0;
      $this->suite->runTest(new SimpleTestCase('succeeds'));
      $this->assertEquals(1, SimpleTestCase::$init);
    }    

    /**
     * Tests all tests from a test class (but not those of others) are 
     * marked as skipped when its beforeClass method throws an exception.
     *
     */    
    #[@test]
    public function beforeClassFails() {
      SimpleTestCase::$init= -1;
      $this->suite->addTest(new SimpleTestCase('fails'));
      $this->suite->addTest(new SimpleTestCase('succeeds'));
      $this->suite->addTest(new AnotherTestCase('succeeds'));
      $this->suite->addTest(new SimpleTestCase('skipped'));
      $this->suite->addTest(new SimpleTestCase('ignored'));
      $r= $this->suite->run();
      $this->assertEquals(4, $r->skipCount(), 'skipCount');
      $this->assertEquals(1, $r->successCount(), 'successCount');
    }    

    /**
     * Tests method decorated with afterClass is executed
     *
     */    
    #[@test]
    public function runInvokesAfterClass() {
      SimpleTestCase::$dispose= 0;
      $this->suite->addTest(new SimpleTestCase('fails'));
      $this->suite->addTest(new SimpleTestCase('succeeds'));
      $this->suite->run();
      $this->assertEquals(1, SimpleTestCase::$dispose);
    }    

    /**
     * Tests method decorated with afterClass is executed
     *
     */    
    #[@test]
    public function runTestInvokesAfterClass() {
      SimpleTestCase::$dispose= 0;
      $this->suite->runTest(new SimpleTestCase('succeeds'));
      $this->assertEquals(1, SimpleTestCase::$dispose);
    }    
  }
?>
