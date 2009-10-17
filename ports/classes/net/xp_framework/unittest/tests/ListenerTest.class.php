<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'unittest.TestSuite',
    'util.collections.HashTable',
    'lang.types.String', 
    'lang.types.ArrayList',
    'net.xp_framework.unittest.tests.SimpleTestCase'
  );

  /**
   * TestCase
   *
   * @see      xp://unittest.TestListener
   * @purpose  Unittest
   */
  class ListenerTest extends TestCase implements TestListener {
    protected
      $suite        = NULL,
      $invocations  = NULL;  
      
    /**
     * Setup method. Creates a new test suite.
     *
     */
    public function setUp() {
      $this->invocations= create('new util.collections.HashTable<lang.types.String, lang.types.ArrayList>()');
      $this->suite= new TestSuite();
      $this->suite->addListener($this);
    }

    /**
     * Setup method. Creates a new test suite.
     *
     */
    public function tearDown() {
      $this->suite->removeListener($this);
    }
    
    /**
     * Called when a test case starts.
     *
     * @param   unittest.TestCase failure
     */
    public function testStarted(TestCase $case) {
      $this->invocations[__FUNCTION__]= new ArrayList($case);
    }

    /**
     * Called when a test fails.
     *
     * @param   unittest.TestFailure failure
     */
    public function testFailed(TestFailure $failure) {
      $this->invocations[__FUNCTION__]= new ArrayList($failure);
    }

    /**
     * Called when a test errors.
     *
     * @param   unittest.TestFailure error
     */
    public function testError(TestError $error) {
      $this->invocations[__FUNCTION__]= new ArrayList($error);
    }

    /**
     * Called when a test raises warnings.
     *
     * @param   unittest.TestWarning warning
     */
    public function testWarning(TestWarning $warning) {
      $this->invocations[__FUNCTION__]= new ArrayList($warning);
    }

    /**
     * Called when a test finished successfully.
     *
     * @param   unittest.TestSuccess success
     */
    public function testSucceeded(TestSuccess $success) {
      $this->invocations[__FUNCTION__]= new ArrayList($success);
    }

    /**
     * Called when a test is not run because it is skipped due to a 
     * failed prerequisite.
     *
     * @param   unittest.TestSkipped skipped
     */
    public function testSkipped(TestSkipped $skipped) {
      $this->invocations[__FUNCTION__]= new ArrayList($skipped);
    }

    /**
     * Called when a test is not run because it has been ignored by using
     * the @ignore annotation.
     *
     * @param   unittest.TestSkipped ignore
     */
    public function testNotRun(TestSkipped $ignore) {
      $this->invocations[__FUNCTION__]= new ArrayList($ignore);
    }

    /**
     * Called when a test run starts.
     *
     * @param   unittest.TestSuite suite
     */
    public function testRunStarted(TestSuite $suite) {
      $this->invocations[__FUNCTION__]= new ArrayList($suite);
    }

    /**
     * Called when a test run finishes.
     *
     * @param   unittest.TestSuite suite
     * @param   unittest.TestResult result
     */
    public function testRunFinished(TestSuite $suite, TestResult $result) {
      $this->invocations[__FUNCTION__]= new ArrayList($suite, $result);
    }

    /**
     * Tests running a single test that succeeds.
     *
     */    
    #[@test]
    public function notifiedOnSuccess() {
      with ($case= new SimpleTestCase('succeeds')); {
        $this->suite->runTest($case);
        $this->assertEquals($this->suite, $this->invocations['testRunStarted'][0]);
        $this->assertEquals($case, $this->invocations['testStarted'][0]);
        $this->assertSubclass($this->invocations['testSucceeded'][0], 'unittest.TestSuccess');
        $this->assertEquals($this->suite, $this->invocations['testRunFinished'][0]);
        $this->assertClass($this->invocations['testRunFinished'][1], 'unittest.TestResult');
      }
    }    

    /**
     * Tests running a single test that fails.
     *
     */    
    #[@test]
    public function notifiedOnFailure() {
      with ($case= new SimpleTestCase('fails')); {
        $this->suite->runTest($case);
        $this->assertEquals($this->suite, $this->invocations['testRunStarted'][0]);
        $this->assertEquals($case, $this->invocations['testStarted'][0]);
        $this->assertSubclass($this->invocations['testFailed'][0], 'unittest.TestFailure');
        $this->assertEquals($this->suite, $this->invocations['testRunFinished'][0]);
        $this->assertClass($this->invocations['testRunFinished'][1], 'unittest.TestResult');
      }
    }    

    /**
     * Tests running a single test that throws an exception.
     *
     */    
    #[@test]
    public function notifiedOnException() {
      with ($case= new SimpleTestCase('throws')); {
        $this->suite->runTest($case);
        $this->assertEquals($this->suite, $this->invocations['testRunStarted'][0]);
        $this->assertEquals($case, $this->invocations['testStarted'][0]);
        $this->assertSubclass($this->invocations['testError'][0], 'unittest.TestFailure');
        $this->assertEquals($this->suite, $this->invocations['testRunFinished'][0]);
        $this->assertClass($this->invocations['testRunFinished'][1], 'unittest.TestResult');
      }
    }    

    /**
     * Tests running a single test that raises an error.
     *
     */    
    #[@test]
    public function notifiedOnError() {
      with ($case= new SimpleTestCase('raisesAnError')); {
        $this->suite->runTest($case);
        $this->assertEquals($this->suite, $this->invocations['testRunStarted'][0]);
        $this->assertEquals($case, $this->invocations['testStarted'][0]);
        $this->assertSubclass($this->invocations['testWarning'][0], 'unittest.TestFailure');
        $this->assertEquals($this->suite, $this->invocations['testRunFinished'][0]);
        $this->assertClass($this->invocations['testRunFinished'][1], 'unittest.TestResult');
      }
    }    

    /**
     * Tests running a single test that is skipped due to not-met
     * prerequisites.
     *
     */    
    #[@test]
    public function notifiedOnSkipped() {
      with ($case= new SimpleTestCase('skipped')); {
        $this->suite->runTest($case);
        $this->assertEquals($this->suite, $this->invocations['testRunStarted'][0]);
        $this->assertEquals($case, $this->invocations['testStarted'][0]);
        $this->assertSubclass($this->invocations['testSkipped'][0], 'unittest.TestSkipped');
        $this->assertEquals($this->suite, $this->invocations['testRunFinished'][0]);
        $this->assertClass($this->invocations['testRunFinished'][1], 'unittest.TestResult');
      }
    }    

    /**
     * Tests running a single test that is ignored because it has
     * an @ignore annotation.
     *
     */    
    #[@test]
    public function notifiedOnIgnored() {
      with ($case= new SimpleTestCase('ignored')); {
        $this->suite->runTest($case);
        $this->assertEquals($this->suite, $this->invocations['testRunStarted'][0]);
        $this->assertEquals($case, $this->invocations['testStarted'][0]);
        $this->assertSubclass($this->invocations['testNotRun'][0], 'unittest.TestSkipped');
        $this->assertEquals($this->suite, $this->invocations['testRunFinished'][0]);
        $this->assertClass($this->invocations['testRunFinished'][1], 'unittest.TestResult');
      }
    }    
  }
?>
