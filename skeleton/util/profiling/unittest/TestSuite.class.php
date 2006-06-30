<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.profiling.Timer',
    'util.profiling.unittest.TestCase',
    'util.profiling.unittest.TestResult',
    'util.NoSuchElementException'
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
   *   $suite= &new TestSuite();
   *   $suite->addTest(new DBTest('testConnect'));
   *   $suite->addTest(new DBTest('testSelect'));
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
     * @throws  lang.IllegalArgumentException in case given argument is not a testcase
     */
    function &addTest(&$test) {
      if (!is_a($test, 'TestCase')) {
        return throw(new IllegalArgumentException('Given argument is not a TestCase ('.xp::typeOf($test).')'));
      }
      $this->tests[]= &$test;
      return $test;
    }

    /**
     * Add a test class
     *
     * @access  public
     * @param   &lang.XPClass<util.profiling.unittest.TestCase> class
     * @return  lang.reflect.Method[] ignored test methods
     * @throws  lang.IllegalArgumentException in case given argument is not a testcase class
     * @throws  util.NoSuchElementException in case given testcase class does not contain any tests
     */
    function addTestClass(&$class, $arguments= array()) {
      if (!$class->isSubclassOf('TestCase')) {
        return throw(new IllegalArgumentException('Given argument is not a TestCase class ('.xp::stringOf($class).')'));
      }

      $ignored= array();
      for ($methods= $class->getMethods(), $i= 0, $s= sizeof($methods); $i < $s; $i++) {
        if (!$methods[$i]->hasAnnotation('test')) continue;

        if ($methods[$i]->hasAnnotation('ignore')) {
          $ignored[]= &$methods[$i];
          continue;
        }

        // Add test method
        $this->addTest(call_user_func_array(array(&$class, 'newInstance'), array_merge(
          (array)$methods[$i]->getName(TRUE),
          $arguments
        )));
      }

      if (0 == $this->numTests()) {
        return throw(new NoSuchElementException('No tests found in ', $class->getName()));
      }

      return $ignored;
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
