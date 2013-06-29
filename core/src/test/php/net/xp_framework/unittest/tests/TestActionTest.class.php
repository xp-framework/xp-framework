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
     * Setup method. Creates a new test suite.
     */
    public function setUp() {
      $this->suite= new TestSuite();
    }

    #[@test]
    public function methods_invocation_order() {
      ClassLoader::defineClass('net.xp_framework.unittest.tests.RecordActionInvocation', 'lang.Object', array('unittest.TestAction'), '{
        public function beforeTest(TestCase $t) {
          $t->run[]= "before";
        }

        public function afterTest(TestCase $t) {
          $t->run[]= "after";
        }
      }');
      $test= newinstance('unittest.TestCase', array('fixture'), '{
        public $run= array();

        public function setUp() {
          $this->run[]= "setup";
        }

        public function tearDown() {
          $this->run[]= "teardown";
        }

        #[@test, @action("net.xp_framework.unittest.tests.RecordActionInvocation")]
        public function fixture() {
          $this->run[]= "test";
        }
      }');
      $this->suite->runTest($test);
      $this->assertEquals(array('before', 'setup', 'test', 'teardown', 'after'), $test->run);
    }
  }
?>
