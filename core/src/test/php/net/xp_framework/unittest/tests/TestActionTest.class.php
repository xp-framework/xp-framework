<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestCase');

  /**
   * Test test actions
   */
  class TestActionTest extends TestCase {
    protected $suite= NULL;

    /**
     * Defines RecordActionInvocation class used in this test
     */
    #[@beforeClass]
    public static function defineActionInvocataionRecorder() {
      ClassLoader::defineClass('net.xp_framework.unittest.tests.RecordActionInvocation', 'lang.Object', array('unittest.TestAction'), '{
        public function beforeTest(TestCase $t) {
          $t->run[]= "before";
        }

        public function afterTest(TestCase $t) {
          $t->run[]= "after";
        }
      }');
    }

    /**
     * Setup method. Creates a new test suite.
     */
    public function setUp() {
      $this->suite= new TestSuite();
    }

    #[@test]
    public function beforeTest_and_afterTest_invocation_order() {
      $test= newinstance('unittest.TestCase', array('fixture'), '{
        public $run= array();

        #[@test, @action("net.xp_framework.unittest.tests.RecordActionInvocation")]
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

        #[@test, @action("net.xp_framework.unittest.tests.RecordActionInvocation")]
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

        #[@test, @action("net.xp_framework.unittest.tests.RecordActionInvocation")]
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
        #[@test, @action("net.xp_framework.unittest.tests.SkipThisTest")]
        public function fixture() {
          throw new IllegalStateException("This test should have been skipped");
        }
      }');
      $r= $this->suite->runTest($test);
      $this->assertEquals(1, $r->skipCount());
    }
  }
?>
