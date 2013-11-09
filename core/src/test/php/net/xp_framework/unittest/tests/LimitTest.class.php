<?php namespace net\xp_framework\unittest\tests;
 
/**
 * Test TestSuite class methods
 *
 * @see    xp://unittest.TestSuite
 */
class LimitTest extends \unittest\TestCase {
  protected $suite= null;
    
  /**
   * Setup method. Creates a new test suite.
   */
  public function setUp() {
    $this->suite= new \unittest\TestSuite();
  }

  #[@test]
  public function timeouts() {
    $r= $this->suite->runTest(new SimpleTestCase('timeouts'));
    $this->assertEquals(1, $r->failureCount());
  }    

  #[@test]
  public function noTimeout() {
    $r= $this->suite->runTest(new SimpleTestCase('noTimeout'));
    $this->assertEquals(1, $r->successCount());
  }    
}
