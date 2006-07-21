<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'util.profiling.unittest.TestCase', 
    'util.profiling.unittest.TestSuite', 
    'net.xp_framework.unittest.tests.SimpleTestCase'
  );

  /**
   * Test TestSuite class methods
   *
   * @see      xp://util.profiling.unittest.TestSuite
   * @purpose  Unit Test
   */
  class LimitTest extends TestCase {
    public
      $suite= NULL;
      
    /**
     * Setup method. Creates a new test suite.
     *
     * @access  public
     */
    public function setUp() {
      $this->suite= &new TestSuite();
    }

    /**
     * Tests running the test that times out
     *
     * @access  public
     */    
    #[@test]
    public function timeouts() {
      $r= &$this->suite->runTest(new SimpleTestCase('timeouts'));
      $this->assertEquals(1, $r->failureCount());
    }    

    /**
     * Tests running the test that doesn't timeout
     *
     * @access  public
     */    
    #[@test]
    public function noTimeout() {
      $r= &$this->suite->runTest(new SimpleTestCase('noTimeout'));
      $this->assertEquals(1, $r->successCount());
    }    
  }
?>
