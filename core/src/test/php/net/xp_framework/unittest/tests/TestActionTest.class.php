<?php namespace net\xp_framework\unittest\tests;

use lang\ClassLoader;
use lang\XPClass;

/**
 * Test test actions
 */
class TestActionTest extends \unittest\TestCase {
  protected $suite= NULL;

  /**
   * Setup method. Creates a new test suite.
   */
  public function setUp() {
    $this->suite= new \unittest\TestSuite();
  }

  #[@test]
  public function beforeTest_and_afterTest_invocation_order() {
    $test= newinstance('unittest.TestCase', array('fixture'), '{
      public $run= array();

      #[@test, @action(new \net\xp_framework\unittest\tests\RecordActionInvocation("run"))]
      public function fixture() {
        $this->run[]= "test";
      }
    }');
    $this->suite->runTest($test);
    $this->assertEquals(array('before', 'test', 'after'), $test->run);
  }

  #[@test]
  public function beforeTest_is_invoked_before_setUp() {
    $test= newinstance('unittest.TestCase', array('fixture'), '{
      public $run= array();

      public function setUp() {
        $this->run[]= "setup";
      }

      #[@test, @action(new \net\xp_framework\unittest\tests\RecordActionInvocation("run"))]
      public function fixture() {
        $this->run[]= "test";
      }
    }');
    $this->suite->runTest($test);
    $this->assertEquals(array('before', 'setup', 'test', 'after'), $test->run);
  }

  #[@test]
  public function afterTest_is_invoked_after_tearDown() {
    $test= newinstance('unittest.TestCase', array('fixture'), '{
      public $run= array();

      public function tearDown() {
        $this->run[]= "teardown";
      }

      #[@test, @action(new \net\xp_framework\unittest\tests\RecordActionInvocation("run"))]
      public function fixture() {
        $this->run[]= "test";
      }
    }');
    $this->suite->runTest($test);
    $this->assertEquals(array('before', 'test', 'teardown', 'after'), $test->run);
  }

  #[@test]
  public function beforeTest_can_skip_test() {
    ClassLoader::defineClass('net.xp_framework.unittest.tests.SkipThisTest', 'lang.Object', array('unittest.TestAction'), '{
      public function beforeTest(TestCase $t) {
        throw new PrerequisitesNotMetError("Skip");
      }

      public function afterTest(TestCase $t) {
        // NOOP
      }
    }');
    $test= newinstance('unittest.TestCase', array('fixture'), '{
      #[@test, @action(new \net\xp_framework\unittest\tests\SkipThisTest())]
      public function fixture() {
        throw new IllegalStateException("This test should have been skipped");
      }
    }');
    $r= $this->suite->runTest($test);
    $this->assertEquals(1, $r->skipCount());
  }

  #[@test]
  public function invocation_order_with_class_annotation() {
    $this->suite->addTestClass(XPClass::forName('net.xp_framework.unittest.tests.TestWithAction'));
    $this->suite->run();
    $this->assertEquals(
      array('before', 'one', 'after', 'before', 'two', 'after'),
      array_merge($this->suite->testAt(0)->run, $this->suite->testAt(1)->run)
    );
  }

  #[@test]
  public function test_action_with_arguments() {
    ClassLoader::defineClass('net.xp_framework.unittest.tests.PlatformVerification', 'lang.Object', array('unittest.TestAction'), '{
      protected $platform;

      public function __construct($platform) {
        $this->platform= $platform;
      }

      public function beforeTest(TestCase $t) {
        if (PHP_OS !== $this->platform) {
          throw new PrerequisitesNotMetError("Skip", NULL, $this->platform);
        }
      }

      public function afterTest(TestCase $t) {
        // NOOP
      }
    }');
    $test= newinstance('unittest.TestCase', array('fixture'), '{
      #[@test, @action(new \net\xp_framework\unittest\tests\PlatformVerification("Test"))]
      public function fixture() {
        throw new IllegalStateException("This test should have been skipped");
      }
    }');
    $outcome= $this->suite->runTest($test)->outcomeOf($test);
    $this->assertInstanceOf('unittest.TestPrerequisitesNotMet', $outcome);
    $this->assertEquals(array('Test'), $outcome->reason->prerequisites);
  }

  #[@test]
  public function multiple_actions() {
    $test= newinstance('unittest.TestCase', array('fixture'), '{
      public $one= array(), $two= array();

      #[@test, @action([
      #  new \net\xp_framework\unittest\tests\RecordActionInvocation("one"),
      #  new \net\xp_framework\unittest\tests\RecordActionInvocation("two")
      #])]
      public function fixture() {
      }
    }');
    $this->suite->runTest($test);
    $this->assertEquals(
      array('one' => array('before', 'after'), 'two' => array('before', 'after')),
      array('one' =>  $test->one, 'two' => $test->two)
    );
  }
}
