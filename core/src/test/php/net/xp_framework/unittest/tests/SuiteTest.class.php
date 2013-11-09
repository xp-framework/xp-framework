<?php namespace net\xp_framework\unittest\tests;
 
/**
 * Test TestSuite class methods
 *
 * @see    xp://unittest.TestSuite
 */
class SuiteTest extends \unittest\TestCase {
  protected $suite= null;
    
  /**
   * Setup method. Creates a new test suite.
   */
  public function setUp() {
    $this->suite= new \unittest\TestSuite();
  }

  #[@test]
  public function initallyEmpty() {
    $this->assertEquals(0, $this->suite->numTests());
  }    

  #[@test]
  public function addingATest() {
    $this->suite->addTest($this);
    $this->assertEquals(1, $this->suite->numTests());
  }    

  #[@test]
  public function addingATestTwice() {
    $this->suite->addTest($this);
    $this->suite->addTest($this);
    $this->assertEquals(2, $this->suite->numTests());
  }    

  #[@test, @expect('lang.IllegalArgumentException')]
  public function addNonTest() {
    $this->suite->addTest(new \lang\Object());
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function runNonTest() {
    $this->suite->runTest(new \lang\Object());
  }

  #[@test, @expect('lang.MethodNotImplementedException')]
  public function addInvalidTest() {
    $this->suite->addTest(newinstance('unittest.TestCase', array('nonExistant'), '{}'));
  }

  #[@test, @expect('lang.MethodNotImplementedException')]
  public function runInvalidTest() {
    $this->suite->runTest(newinstance('unittest.TestCase', array('nonExistant'), '{}'));
  }

  #[@test]
  public function addingATestClass() {
    $ignored= $this->suite->addTestClass(\lang\XPClass::forName('net.xp_framework.unittest.tests.SimpleTestCase'));
    $this->assertNotEmpty($ignored);
    for ($i= 0, $s= $this->suite->numTests(); $i < $s; $i++) {
      $this->assertSubclass($this->suite->testAt($i), 'unittest.TestCase');
    }
  }

  #[@test]
  public function addingATestClassTwice() {
    $class= \lang\XPClass::forName('net.xp_framework.unittest.tests.SimpleTestCase');
    $this->suite->addTestClass($class);
    $n= $this->suite->numTests();
    $this->suite->addTestClass($class);
    $this->assertEquals($n * 2, $this->suite->numTests());
  }

  #[@test, @expect('util.NoSuchElementException')]
  public function addingEmptyTest() {
    $this->suite->addTestClass(\lang\XPClass::forName('net.xp_framework.unittest.tests.EmptyTestCase'));
  }    

  #[@test]
  public function addingEmptyTestAfter() {
    $this->suite->addTestClass(\lang\XPClass::forName('net.xp_framework.unittest.tests.SimpleTestCase'));
    $before= $this->suite->numTests();
    try {
      $this->suite->addTestClass(\lang\XPClass::forName('net.xp_framework.unittest.tests.EmptyTestCase'));
      $this->fail('Expected exception not thrown', null, 'util.NoSuchElementException');
    } catch (\util\NoSuchElementException $expected) { 
    }
    $this->assertEquals($before, $this->suite->numTests());
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function addingANonTestClass() {
    $this->suite->addTestClass(\lang\XPClass::forName('lang.Object'));
  }    

  #[@test]
  public function clearingTests() {
    $this->suite->addTest($this);
    $this->assertEquals(1, $this->suite->numTests());
    $this->suite->clearTests();
    $this->assertEquals(0, $this->suite->numTests());
  }

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

  #[@test]
  public function runInvokesBeforeClassOneClass() {
    SimpleTestCase::$init= 0;
    $this->suite->addTest(new SimpleTestCase('fails'));
    $this->suite->addTest(new SimpleTestCase('succeeds'));
    $this->suite->run();
    $this->assertEquals(1, SimpleTestCase::$init);
  }

  #[@test]
  public function runInvokesBeforeClassMultipleClasses() {
    SimpleTestCase::$init= 0;
    $this->suite->addTest(new SimpleTestCase('fails'));
    $this->suite->addTest(new AnotherTestCase('succeeds'));
    $this->suite->addTest(new SimpleTestCase('succeeds'));
    $this->suite->run();
    $this->assertEquals(1, SimpleTestCase::$init);
  }

  #[@test]
  public function runTestInvokesBeforeClass() {
    SimpleTestCase::$init= 0;
    $this->suite->runTest(new SimpleTestCase('succeeds'));
    $this->assertEquals(1, SimpleTestCase::$init);
  }    

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

  #[@test]
  public function beforeClassRaisesAPrerequisitesNotMet() {
    $t= newinstance('unittest.TestCase', array('irrelevant'), '{
      #[@beforeClass]
      public static function raise() {
        throw new PrerequisitesNotMetError("Cannot run");
      }
      
      #[@test]
      public function irrelevant() {
        $this->assertEquals(1, 0);
      }
    }');
    $this->suite->addTest($t);
    $r= $this->suite->run();
    $this->assertEquals(1, $r->skipCount(), 'skipCount');
    $this->assertClass($r->outcomeOf($t), 'unittest.TestPrerequisitesNotMet');
    $this->assertClass($r->outcomeOf($t)->reason, 'unittest.PrerequisitesNotMetError');
    $this->assertEquals('Cannot run', $r->outcomeOf($t)->reason->getMessage());
  }    

  #[@test]
  public function beforeClassRaisesAnException() {
    $t= newinstance('unittest.TestCase', array('irrelevant'), '{
      #[@beforeClass]
      public static function raise() {
        throw new IllegalStateException("Skip");
      }
      
      #[@test]
      public function irrelevant() {
        $this->assertEquals(1, 0);
      }
    }');
    $this->suite->addTest($t);
    $r= $this->suite->run();
    $this->assertEquals(1, $r->skipCount(), 'skipCount');
    $this->assertClass($r->outcomeOf($t), 'unittest.TestPrerequisitesNotMet');
    $this->assertClass($r->outcomeOf($t)->reason, 'unittest.PrerequisitesNotMetError');
    $this->assertEquals('Exception in beforeClass method raise', $r->outcomeOf($t)->reason->getMessage());
  }    

  #[@test]
  public function runInvokesAfterClass() {
    SimpleTestCase::$dispose= 0;
    $this->suite->addTest(new SimpleTestCase('fails'));
    $this->suite->addTest(new SimpleTestCase('succeeds'));
    $this->suite->run();
    $this->assertEquals(1, SimpleTestCase::$dispose);
  }    

  #[@test]
  public function runTestInvokesAfterClass() {
    SimpleTestCase::$dispose= 0;
    $this->suite->runTest(new SimpleTestCase('succeeds'));
    $this->assertEquals(1, SimpleTestCase::$dispose);
  }    

  #[@test]
  public function warningsMakeTestFail() {
    with ($test= new SimpleTestCase('raisesAnError')); {
      $this->assertEquals(
        array('"Undefined variable: a" in net\xp_framework\unittest\tests\SimpleTestCase::raisesAnError() (SimpleTestCase.class.php, line 64, occured once)'), 
        $this->suite->runTest($test)->failed[$test->hashCode()]->reason
      );
    }
  }

  #[@test]
  public function exceptionsMakeTestFail() {
    with ($test= new SimpleTestCase('throws')); {
      $this->assertClass(
        $this->suite->runTest($test)->failed[$test->hashCode()]->reason,
        'lang.IllegalArgumentException'
      );
    }
  }

  #[@test]
  public function expectedExceptionsWithWarningsMakeTestFail() {
    with ($test= new SimpleTestCase('catchExpectedWithWarning')); {
      $this->assertEquals(
        array('"Undefined variable: a" in net\xp_framework\unittest\tests\SimpleTestCase::catchExpectedWithWarning() (SimpleTestCase.class.php, line 118, occured once)'), 
        $this->suite->runTest($test)->failed[$test->hashCode()]->reason
      );
    }
  }
  
  #[@test]
  public function warningsDontAffectSucceedingTests() {
    $this->suite->addTest(new SimpleTestCase('raisesAnError'));
    $this->suite->addTest(new SimpleTestCase('succeeds'));
    $r= $this->suite->run();
    $this->assertEquals(1, $r->failureCount());
    $this->assertEquals(1, $r->successCount());
  }
 
  #[@test]
  public function warningsFromFailuresDontAffectSucceedingTests() {
    $this->suite->addTest(new SimpleTestCase('raisesAnErrorAndFails'));
    $this->suite->addTest(new SimpleTestCase('succeeds'));
    $r= $this->suite->run();
    $this->assertEquals(1, $r->failureCount());
    $this->assertEquals(1, $r->successCount());
  }

  #[@test]
  public function warningsFromSetupDontAffectSucceedingTests() {
    $this->suite->addTest(new SimpleTestCase('raisesAnErrorInSetup'));
    $this->suite->addTest(new SimpleTestCase('succeeds'));
    $r= $this->suite->run();
    $this->assertEquals(1, $r->successCount());
  }

  #[@test]
  public function expectedException() {
    $this->suite->addTest(new SimpleTestCase('catchExpected'));
    $r= $this->suite->run();
    $this->assertEquals(1, $r->successCount());
  }

  #[@test]
  public function subclassOfExpectedException() {
    $this->suite->addTest(new SimpleTestCase('catchSubclassOfExpected'));
    $r= $this->suite->run();
    $this->assertEquals(1, $r->successCount());
  }

  #[@test]
  public function expectedExceptionNotThrown() {
    $this->suite->addTest(new SimpleTestCase('expectedExceptionNotThrown'));
    $r= $this->suite->run();
    $this->assertEquals(1, $r->failureCount());
    $this->assertEquals(
      'Expected exception not caught', 
      cast($r->outcomeOf($this->suite->testAt(0)), 'unittest.TestFailure')->reason->getMessage()
    );
  }

  #[@test]
  public function catchExpectedWithMessage() {
    $this->suite->addTest(new SimpleTestCase('catchExpectedWithMessage'));
    $r= $this->suite->run();
    $this->assertEquals(1, $r->successCount());
  }

  #[@test]
  public function catchExpectedWithMismatchingMessage() {
    $this->suite->addTest(new SimpleTestCase('catchExpectedWithWrongMessage'));
    $r= $this->suite->run();
    $this->assertEquals(1, $r->failureCount());
    $this->assertEquals(
      'Expected lang.IllegalArgumentException\'s message differs',
      cast($r->outcomeOf($this->suite->testAt(0)), 'unittest.TestFailure')->reason->getMessage()
    );
  }

  #[@test]
  public function catchExpectedWithPatternMessage() {
    $this->suite->addTest(new SimpleTestCase('catchExpectedWithPatternMessage'));
    $r= $this->suite->run();
    $this->assertEquals(1, $r->successCount());
  }

  #[@test]
  public function catchExceptionsDuringSetUpOfTestDontBringDownTestSuite() {
    $this->suite->addTest(new SetUpFailingTestCase('emptyTest'));
    $r= $this->suite->run();
    $this->assertEquals(1, $r->failureCount());
  }

  #[@test]
  public function doFail() {
    $this->suite->addTest(new SimpleTestCase('doFail'));
    $r= $this->suite->run();
    $this->assertEquals(1, $r->failureCount());
  }

  #[@test]
  public function doSkip() {
    $this->suite->addTest(new SimpleTestCase('doSkip'));
    $r= $this->suite->run();
    $this->assertEquals(1, $r->skipCount());
  }
}
