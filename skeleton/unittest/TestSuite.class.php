<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'unittest.TestResult',
    'util.NoSuchElementException'
  );

  /**
   * Test suite
   *
   * Example:
   * <code>
   *   uses(
   *     'unittest.TestSuite', 
   *     'net.xp_framework.unittest.rdbms.DBTest'
   *   );
   *   
   *   $suite= new TestSuite();
   *   $suite->addTest(new DBTest('testConnect'));
   *   $suite->addTest(new DBTest('testSelect'));
   *   
   *   echo $suite->run()->toString();
   * </code>
   *
   * @see      http://junit.sourceforge.net/doc/testinfected/testing.htm
   * @purpose  Testcase container
   */
  class TestSuite extends Object {
    public
      $tests    = array();

    /**
     * Add a test
     *
     * @param   unittest.TestCase test
     * @return  unittest.TestCase
     * @throws  lang.IllegalArgumentException in case given argument is not a testcase
     */
    public function addTest(TestCase $test) {
      $this->tests[]= $test;
      return $test;
    }

    /**
     * Add a test class
     *
     * @param   lang.XPClass<unittest.TestCase> class
     * @return  lang.reflect.Method[] ignored test methods
     * @throws  lang.IllegalArgumentException in case given argument is not a testcase class
     * @throws  util.NoSuchElementException in case given testcase class does not contain any tests
     */
    public function addTestClass($class, $arguments= array()) {
      if (!$class->isSubclassOf('unittest.TestCase')) {
        throw(new IllegalArgumentException('Given argument is not a TestCase class ('.xp::stringOf($class).')'));
      }

      $ignored= array();
      for ($methods= $class->getMethods(), $i= 0, $s= sizeof($methods); $i < $s; $i++) {
        if (!$methods[$i]->hasAnnotation('test')) continue;

        if ($methods[$i]->hasAnnotation('ignore')) {
          $ignored[]= $methods[$i];
          continue;
        }

        // Add test method
        $this->addTest(call_user_func_array(array($class, 'newInstance'), array_merge(
          (array)$methods[$i]->getName(TRUE),
          $arguments
        )));
      }

      if (0 == $this->numTests()) {
        throw(new NoSuchElementException('No tests found in ', $class->getName()));
      }

      return $ignored;
    }
    
    /**
     * Returns number of tests in this suite
     *
     * @return  int
     */
    public function numTests() {
      return sizeof($this->tests);
    }
    
    /**
     * Remove all tests
     *
     */
    public function clearTests() {
      $this->tests= array();
    }
    
    /**
     * Returns test at a given position
     *
     * @param   int pos
     * @return  unittest.TestCase or NULL if none was found
     */
    public function testAt($pos) {
      if (isset($this->tests[$pos])) return $this->tests[$pos]; else return NULL;
    }
    
    /**
     * Run a single test
     *
     * @param   unittest.TestCase test
     * @return  unittest.TestResult
     */
    public function runTest($test) {
      $result= new TestResult();
      $test->run($result);
      return $result;
    }
    
    /**
     * Run this test suite
     *
     * @return  unittest.TestResult
     */
    public function run() {
      $result= new TestResult();
      for ($i= 0, $s= sizeof($this->tests); $i < $s; $i++) {
        $this->tests[$i]->run($result);
      }

      return $result;
    }
  }
?>
