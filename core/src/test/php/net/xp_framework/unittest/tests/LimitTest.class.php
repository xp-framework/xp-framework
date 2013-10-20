<?php namespace net\xp_framework\unittest\tests;
 
use unittest\TestCase;
use unittest\TestSuite;


/**
 * Test TestSuite class methods
 *
 * @see      xp://unittest.TestSuite
 * @purpose  Unit Test
 */
class LimitTest extends TestCase {
  public
    $suite= null;
    
  /**
   * Setup method. Creates a new test suite.
   *
   */
  public function setUp() {
    $this->suite= new TestSuite();
  }

  /**
   * Tests running the test that times out
   *
   */    
  #[@test]
  public function timeouts() {
    $r= $this->suite->runTest(new SimpleTestCase('timeouts'));
    $this->assertEquals(1, $r->failureCount());
  }    

  /**
   * Tests running the test that doesn't timeout
   *
   */    
  #[@test]
  public function noTimeout() {
    $r= $this->suite->runTest(new SimpleTestCase('noTimeout'));
    $this->assertEquals(1, $r->successCount());
  }    
}
