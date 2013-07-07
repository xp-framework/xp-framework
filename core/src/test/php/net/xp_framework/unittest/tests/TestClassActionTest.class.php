<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestCase', 'net.xp_framework.unittest.tests.TestWithClassAction');

  /**
   * Test test class actions
   */
  class TestClassActionTest extends TestCase {
    protected $suite= NULL;

    /**
     * Defines RecordActionInvocation class used in this test
     */
    #[@beforeClass]
    public static function defineActionInvocataionRecorder() {
      ClassLoader::defineClass('net.xp_framework.unittest.tests.RecordClassActionInvocation', 'lang.Object', array('unittest.TestClassAction'), '{
        public function beforeTestClass(XPClass $c) {
          $f= $c->getField("run");
          $f->set(NULL, array_merge($f->get(NULL), array("before")));
        }

        public function afterTestClass(XPClass $c) {
          $f= $c->getField("run");
          $f->set(NULL, array_merge($f->get(NULL), array("after")));
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
    public function beforeTestClass_and_afterTestClass_invocation_order() {
      $this->suite->runTest(new TestWithClassAction('fixture'));
      $this->assertEquals(array('before', 'test', 'after'), TestWithClassAction::$run);
    }
  }
?>
