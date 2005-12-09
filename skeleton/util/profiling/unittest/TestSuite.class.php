<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.profiling.unittest.TestCase',
    'util.profiling.unittest.TestResult'
  );

  /**
   * Test suite
   *
   * Example:
   * <code>
   *   uses(
   *     'util.profiling.unittest.TestSuite', 
   *     'net.xp_framework.unittest.rdbms.DBTest'
   *   );
   *   
   *   $dsn= 'sybase://user:password@host/?autoconnect=1';
   *
   *   $suite= &new TestSuite();
   *   $suite->addTest(new DBTest('testConnect', $dsn));
   *   $suite->addTest(new DBTest('testSelect', $dsn));
   *   
   *   $result= &$suite->run();
   *   echo $result->toString();
   * </code>
   *
   * @see      http://junit.sourceforge.net/doc/testinfected/testing.htm
   * @purpose  Testcase container
   */
  class TestSuite extends Object {
    var
      $tests    = array();

    /**
     * Add a test
     *
     * @access  public
     * @param   &util.profiling.unittest.TestCase test
     * @return  &util.profiling.unittest.TestCase
     */
    function &addTest(&$test) {
      $this->tests[]= &$test;
      return $test;
    }
    
    /**
     * Returns number of tests in this suite
     *
     * @access  public
     * @return  int
     */
    function numTests() {
      return sizeof($this->tests);
    }
    
    /**
     * Remove all tests
     *
     * @access  public
     */
    function clearTests() {
      $this->tests= array();
    }
    
    /**
     * Returns test at a given position
     *
     * Example for numTests and testAt:
     * <code>
     *   // [... set up suite ...]
     *
     *   $result= &new TestResult();
     *   for ($i= 0, $s= $suite->numTests(); $i < $s; $i++) {
     *     $suite->runTest($suite->testAt($i), $result);
     *   }
     * </code>
     *
     * @access  public
     * @param   int pos
     * @return  &util.profiling.unittest.TestCase or NULL if none was found
     */
    function &testAt($pos) {
      if (isset($this->tests[$pos])) return $this->tests[$pos]; else return NULL;
    }
    
    /**
     * Run a single test
     *
     * @access  public
     * @param   &util.profiling.unittest.TestCase test
     * @param   &util.profiling.unittest.TestResult result
     * @return  bool success
     */
    function runTest(&$test, &$result) {
      try(); {
        $test->setUp();
      } if (catch('PrerequisitesNotMetError', $e)) {
        $result->setSkipped($test, $e);
        return FALSE;
      } if (catch('AssertionFailedError', $e)) {
        $result->setFailed($test, $e);
        return FALSE;
      }

      try(); {
        $test->run();
      } if (catch('Exception', $e)) {
        $result->setFailed($test, $e);
        $test->tearDown();
        return FALSE;
      }

      $result->setSucceeded($test);
      $test->tearDown();
      return TRUE;
    }
    
    /**
     * Run this test suite
     *
     * @access  public
     * @return  &util.profiling.unittest.TestResult
     */
    function &run() {
      $result= &new TestResult();
      for ($i= 0, $s= sizeof($this->tests); $i < $s; $i++) {
        $this->runTest($this->tests[$i], $result);
      }

      return $result;
    }
  }
?>
