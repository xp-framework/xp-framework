<?php
/* This class is part of the XP framework
 *
 * $Id: LimitTest.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace net::xp_framework::unittest::tests;
 
  ::uses(
    'unittest.TestCase',
    'unittest.TestSuite',
    'net.xp_framework.unittest.tests.SimpleTestCase'
  );

  /**
   * Test TestSuite class methods
   *
   * @see      xp://unittest.TestSuite
   * @purpose  Unit Test
   */
  class LimitTest extends unittest::TestCase {
    public
      $suite= NULL;
      
    /**
     * Setup method. Creates a new test suite.
     *
     */
    public function setUp() {
      $this->suite= new unittest::TestSuite();
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
?>
