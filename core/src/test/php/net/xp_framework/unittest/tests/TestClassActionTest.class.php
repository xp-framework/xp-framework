<?php namespace net\xp_framework\unittest\tests;

/**
 * Test test class actions
 */
class TestClassActionTest extends \unittest\TestCase {
  protected $suite= null;

  /**
   * Setup method. Creates a new test suite.
   */
  public function setUp() {
    $this->suite= new \unittest\TestSuite();
  }

  #[@test]
  public function beforeTestClass_and_afterTestClass_invocation_order() {
    $this->suite->runTest(new TestWithClassAction('fixture'));
    $this->assertEquals(array('before', 'test', 'after'), TestWithClassAction::$run);
  }
}