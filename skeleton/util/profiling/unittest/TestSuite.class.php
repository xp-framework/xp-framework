<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.profiling.Timer',
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
     * @throws  lang.IllegalArgumentException
     */
    function &addTest(&$test) {
      if (!is_a($test, 'TestCase')) {
        return throw(new IllegalArgumentException('Given argument is not a TestCase ('.xp::typeOf($test).')'));
      }
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
     * @return  &util.profiling.unittest.TestResult
     */
    function &runTest(&$test) {
      $result= &new TestResult();
      $test->run($result);
      return $result;
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
        $this->tests[$i]->run($result);
      }

      return $result;
    }
  }
?>
